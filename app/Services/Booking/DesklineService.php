<?php

declare(strict_types=1);

namespace App\Services\Booking;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Saloon\XmlWrangler\XmlReader;

class DesklineService
{
    public function request(string $type = 'GET', string $uri, string $body = null, string $return_key = null)
    {
        $body = str_replace('[DESKLINE_AGENT_CODE]', env('DESKLINE_AGENT_CODE'), $body);
        $body = str_replace('[DESKLINE_HOTEL_CODE]', env('DESKLINE_HOTEL_CODE'), $body);

        $response = Http::withHeaders([
            'Content-Type' => 'text/xml;charset=utf-8',
            'SOAPAction' => 'http://tempuri.org/GetResponse',
        ])->send($type, env('DESKLINE_URL') . $uri, [
            'body' => '<?xml version="1.0" encoding="utf-8"?>
            <soap12:Envelope xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
                <soap12:Body>
                    <GetData xmlns="http://tempuri.org/">
                        <xmlString>
                            <![CDATA['. $body .']]>
                        </xmlString>
                    </GetData>
                </soap12:Body>
            </soap12:Envelope>'
        ]);

        if ($response->status() != 200) {
            throw new \Exception('Deskline request failed');
        }

        $xml = XmlReader::fromString($response->body());
        return $xml->values();
        $array = $xml->values()['soap:Envelope']['soap:Body']['GetDataResponse']['GetDataResult'];

        if (!$array) {
            throw new \Exception('Deskline request failed');
        }

        $data = $this->xmlToArray($array);

        if ($data['@attributes'] && $data['@attributes']['Message'] != 'OK') {
            throw new \Exception('Deskline request failed because of ' . $data['@attributes']['Message']);
        }

        return $return_key ? $data[$return_key] : $data;
    }

    public function xmlToArray(string $xml_string): mixed
    {
        $doc = ($xml_string);

        if ($doc) {
            $xml = simplexml_load_string($xml_string);
            $json = json_encode($xml);
            return json_decode($json, true);
        }
    }
}
