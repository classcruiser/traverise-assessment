@extends('emails.base')

@section('body')
<tr>
    <td valign="middle" class="hero bg_white" style="padding: 2em 0 2em 0;">
        <table>
            <tr>
                <td>
                    <div class="text" style="padding: 0 2.5em; text-align: center; word-break: break-word; font-size: .9em">
                        <h3>Please verify your email address</h3>
                        <p>Please complete the setup process by clicking the link below to verify your email:</p>
                        <p>
                            <a href="{{ env('AUTH_URL') .'/verify-email/'. $super->verify_key }}" class="link-primary" target="_blank" rel="nofollow">
                                {{ env('AUTH_URL') .'/verify-email/'. $super->verify_key }}
                            </a>
                        </p>
                        <p>Once you verified your email, you can login to the dashboard with your credentials</p>
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr><!-- end tr -->
@endsection
