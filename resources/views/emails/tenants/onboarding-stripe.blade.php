@extends('emails.base')

@section('body')
<tr>
    <td valign="middle" class="hero bg_white" style="padding: 2em 0 2em 0;">
        <table>
            <tr>
                <td>
                    <div class="text" style="padding: 0 2.5em; text-align: left; word-break: break-word; font-size: .9em">
                        <h3>Finish your Stripe onboarding process</h3>
                        <p><b>Dear {{ $tenant->full_name }}</b>,</p>

                        <p>You must finish your Stripe onboarding process before you can use the platform, please click or copy and paste the link below to your browser address bar:</p>
                        <p>
                            <a href="https://{{ env('SYSTEM_URI') .'/stripe-onboarding/'. $tenant->id .'/'. str_replace('acct_', '', $tenant->stripe_account_id) }}" class="link-primary" target="_blank" rel="nofollow">
                                https://{{ env('SYSTEM_URI') .'/stripe-onboarding/'. $tenant->id .'/'. str_replace('acct_', '', $tenant->stripe_account_id) }}
                            </a>
                        </p>
                        <p>Don't hesitate to contact us if you need help during the onboarding process</p>
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr><!-- end tr -->
@endsection
