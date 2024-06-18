@extends('Booking.emails.layout') 

@section('body')
    <tr>
        <td valign="middle" class="hero bg_white" style="padding: 0 0 2em 0;">
            <table width="100%">
                <tr>
                    <td>
                        <div class="text" style="padding: 0 2em; text-align: left; word-break: break-word; font-size: .95em">
                            {!! $template !!}
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr><!-- end tr -->
@endsection