<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
    <tr>
        <td valign="middle" class="bg_light footer email-section">
            <table>
                <tr>
                    <td valign="top" width="100%" style="padding-top: 0;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td style="text-align: center; padding-left: 5px; padding-right: 5px">
                                    <h3 class="heading">{{ $profile->title }}</h3>
                                    <ul>
                                        <li>
                                            <span class="text">
                                                {!! $booking->location->address !!}
                                            </span>
                                        </li>
                                        <li><span class="text">{{ $booking->location->contact_email }}</span></a></li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr><!-- end: tr -->
</table>
