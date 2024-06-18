@extends('emails.base')

@section('body')
<tr>
    <td valign="middle" class="hero bg_white" style="padding: 2em 0 2em 0;">
        <table>
            <tr>
                <td>
                    <div class="text" style="padding: 0 2.5em; text-align: left; word-break: break-word; font-size: .9em">
                        <p><small>(english version below)</small></p>
                        <h3>Hallo von Traverise!</h3>
                        <p>Vielen Dank für Ihr Interesse an unserem Produkt.</p>

                        <p>Gerne senden wir Ihnen, Ihre personifizierten Userdaten für einen Demozugang der Traverise Software. Der Zugang ist eine Woche gültig.</p>
                            
                        <p>
                            User: <b>{{ $user->email }}</b><br />
                            Password: <b>{{ $password }}</b><br />
                            Hier können sie sich einloggen: <b><a href="https://demo.traverise.com/auth/login">https://demo.traverise.com/auth/login</a></b><br />
                            Den Demo-Shop finden sie hier: <b><a href="https://demo.traverise.com/book-now">https://demo.traverise.com/book-now</a></b>

                        </p>
                        
                        <p>Sollten Sie Fragen haben, setzen Sie sich gerne per email oder telefonisch mit uns in Verbindung.</p>
                        
                        <p>Unter dem folgenden Link finden Sie branchenspezifische Präsentationen und detaillierte Manuals zu den einzelnen Funktionen: <a href="https://bit.ly/traverise"><b>https://bit.ly/traverise</b></a></p>
                            
                        <p>Wir hoffen, dass Sie Spaß am Arbeiten mit unserer Booking Software haben und wir Sie überzeugen können, künftig Ihre Buchungen mit Traverise abzuwickeln.</p>

                        <br />

                        <p><small>(english version)</small></p>
                        <h3>Hello from Traverise!</h3>
                        <p>Thank you for your interest in our product.</p>

                        <p>We will gladly send you your personalized user data for a demo access to the Traverise software. The access is valid for one week</p>
                            
                        <p>
                            User: <b>{{ $user->email }}</b><br />
                            Password: <b>{{ $password }}</b><br />
                            You can log in here: <b><a href="https://demo.traverise.com/auth/login">https://demo.traverise.com/auth/login</a></b><br />
                            You can find the demo shop here: <b><a href="https://demo.traverise.com/book-now">https://demo.traverise.com/book-now</a></b>
                        </p>
                        
                        <p>If you have any questions, please contact us by email or phone.</p>
                            
                        <p>Under the following link you will find industry-specific presentations and detailed manuals for the individual functions: <a href="https://bit.ly/traverise"><b>https://bit.ly/traverise</b></a></p>
                            
                        <p>We hope that you enjoy working with our booking software and that we can convince you to handle your bookings with Traverise in the future.</p>
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr><!-- end tr -->
@endsection
