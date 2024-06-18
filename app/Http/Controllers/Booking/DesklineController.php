<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use CodeDredd\Soap\Facades\Soap;
use App\Services\Booking\DesklineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DesklineController extends Controller
{
    public function index()
    {
        $deskline = new DesklineService();
        $response = $deskline->request(
            type: 'POST',
            uri: '/DSI/MappingData.asmx',
            body: '<FeratelDsiRQ xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://interface.deskline.net/DSI/XSD">
                <MappingRequest AgentDutyCode="[DESKLINE_AGENT_CODE]" FilterType="None" />
            </FeratelDsiRQ>',
            return_key: 'MappingResult'
        );

        return response($response);
    }

    public function availability()
    {
        $check_in = '2024-06-10';
        $check_out = '2024-06-14';

        $deskline = new DesklineService();
        $response = $deskline->request(
            type: 'POST',
            uri: '/OTA/ImportAvailability.asmx',
            body: '<OTA_HotelInvCountNotifRQ xmlns="http://www.opentravel.org/OTA/2003/05" TimeStamp="2024-06-05T15:25:16" Target="Production" Version="1.0" PrimaryLangID="en">
                <POS>
                    <Source AgentDutyCode="[DESKLINE_AGENT_CODE]" />
                </POS>
                <Inventories HotelCode="[DESKLINE_HOTEL_CODE]" >
                    <Inventory>
                        <StatusApplicationControl Start="'. $check_in .'" End="'. $check_out .'" InvCode="TRSSRBTP1" IsRoom="1" />
                        <InvCounts>
                            <InvCount CountType="2" Count="0" />
                        </InvCounts>
                    </Inventory>
                </Inventories>
            </OTA_HotelInvCountNotifRQ>',
        );

        return response($response);
    }
}
