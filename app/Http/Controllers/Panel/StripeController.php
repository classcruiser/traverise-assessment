<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mail\OnboardingStripeLink;
use App\Models\Booking\User as UserTenant;
use App\Models\Tenant;
use Stripe;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Mail;

class StripeController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new StripeClient(env('STRIPE_LIVE_SECRET_KEY'));
    }

    public function connect($id)
    {
        $tenant = Tenant::find($id);

        $stripe = new StripeClient(env('STRIPE_LIVE_SECRET_KEY'));

        try {
            $response = $stripe->accounts->create([
                'type' => 'standard',
                'email' => $tenant->email
            ]);
        } catch (Stripe\Exception\InvalidRequestException $e) {
            return response($e->getMessage());
        }

        $tenant->update([
            'stripe_account_id' => $response['id']
        ]);

        return redirect()->route('tenants');
    }

    public function delete($id)
    {
        $tenant = Tenant::find($id);

        $response = $this->client->accounts->delete(
            $tenant->stripe_account_id,
            []
        );

        $tenant->update([
            'stripe_account_id' => null
        ]);

        return redirect()->route('tenants');
    }

    public function onboarding(string $id)
    {
        $tenant = Tenant::find($id);

        try {
            $response = $this->client->accounts->retrieve(
                $tenant->stripe_account_id
            );
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }

        $currently_due = $response['requirements']['currently_due'];

        if (is_array($currently_due) && count($currently_due) >= 1) {
            $tenant->update([
                'stripe_onboarding_process' => 1,
            ]);

            $this->sendOnboardingStripeLinkEmail($tenant);

            return response('This user has not complete the onboarding process. We have sent an email to this tenant to finish their onboarding process.');
        }

        $tenant->update([
            'stripe_onboarding_process' => 0,
        ]);

        return response($response);
    }

    // extract to service later ya!
    protected function sendOnboardingStripeLinkEmail(Tenant $tenant)
    {
        //return (new OnboardingStripeLink($tenant))->render();
        return Mail::to($tenant->email, $tenant->full_name)->send(new OnboardingStripeLink($tenant));
    }
}
