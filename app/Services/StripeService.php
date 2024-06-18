<?php

namespace App\Services;

use Stripe\StripeClient;

class StripeService
{
    protected $client;

    public function __construct()
    {
        $secretKey = config('app.env') == 'production' ? config('stripe.live_secret_key') : config('stripe.test_secret_key');
        $this->client = new StripeClient($secretKey);
    }

    public function getOnboardingStatus(string $account_id): bool
    {
        try {
            $response = $this->client->accounts->retrieve(
                $account_id
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $currently_due = $response['requirements']['currently_due'];

        return (is_array($currently_due) && count($currently_due) >= 1);
    }
}
