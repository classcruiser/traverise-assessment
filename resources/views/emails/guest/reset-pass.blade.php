@extends('emails.base')

@section('body')
    <tr>
        <td valign="middle" class="hero bg_white" style="padding: 2em 0 2em 0;">
            <table>
                <tr>
                    <td>
                        <div class="text" style="padding: 0 2.5em; text-align: center; word-break: break-word; font-size: .9em">
                            <h3>Password reset</h3>
                            <p>Hi {{ $guest->fname.' '.$guest->lname }},
                            There was a request to change your password!
                            If you did not make this request then please ignore this email.
                            Otherwise, please click this link to change your password:
                            <p>
                                <a href="{{ $url }}" class="link-primary" target="_blank" rel="nofollow">
                                    {{ $url }}
                                </a>
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr><!-- end tr -->
@endsection
