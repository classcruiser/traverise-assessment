@extends('emails.base')

@section('body')
<tr>
    <td valign="middle" class="hero bg_white" style="padding: 2em 0 2em 0;">
        <table>
            <tr>
                <td>
                    <div class="text" style="padding: 0 2.5em 1em 2.5em; text-align: center; word-break: break-word; font-size: .9em">
                        <h3>Aloha!</h3>
                        <p>{{ $guest->fname.' '.$guest->lname }} hat gerade einen Rheinriff-Multipass für Sie gekauft!</p>
                        <h1>{{ $activationCode }}</h1>
                        <p>Bitte klicken Sie auf den untenstehenden Link und benutzen Sie den Aktivierungscode.<br />Sobald Sie sich registriert haben, ist der Multipass einsatzbereit.</p>
                        <p>
                            <a href="{{ $url }}" class="link-primary" target="_blank" rel="nofollow">
                                Activate your pass here
                            </a>
                        </p>
                    </div>
                    <hr style="border-bottom: 0; border-top: 1px solid #eaeaea;"/>
                    <div class="text" style="padding: 1em 2.5em 0 2.5em; text-align: center; word-break: break-word; font-size: .9em">
                        <h3>Aloha!</h3>
                        <p>{{ $guest->fname.' '.$guest->lname }} has just bought a Rheinriff-Multipass for you!</p>
                        <h1>{{ $activationCode }}</h1>
                        <p>Please click on the link below and use the activation code.<br />As soon as you registered yourself the Multipass is ready to use.</p>
                        <p>
                            <a href="{{ $url }}" class="link-primary" target="_blank" rel="nofollow">
                                Activate your pass here
                            </a>
                        </p>
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr><!-- end tr -->
@endsection
