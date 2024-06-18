<?php

namespace App\Services\Booking;

use GuzzleHttp\Client;
use Ixudra\Curl\Facades\Curl;

class InventoryService
{
    protected $base_url;
    protected $api_key;
    protected $client;

    public function __construct()
    {
        switch (strtoupper(env('APP_ENV'))) {
            case 'PRODUCTION':
                $this->base_url = 'https://inventory.kimasurf.com';

                break;

            case 'LOCAL':
                $this->base_url = 'https://inventory.test';
                //$this->base_url = 'https://inventory.kimasurf.com';

                break;

            case 'STAGING':
                $this->base_url = 'https://inventory.helloangga.com';

                break;
        }

        $this->api_key = 'F0RG3U6XJ7lr4Q2tfda3QIpOJd2VHElC';

        $this->client = new Client([
            'base_uri' => $this->base_url,
        ]);
    }

    public function post($url, $data)
    {
        return Curl::to($this->base_url.$url)
            ->enableDebug(storage_path('logs/curl.txt'))
            ->withHeader("Api-key: {$this->api_key}")
            ->withHeader('Cache-Control: no-cache')
            ->withHeader('Content-type: application/json')
            ->withData(json_encode($data))
            ->post()
        ;
    }

    public function getBaseUrl()
    {
        return $this->base_url;
    }

    public function registerTransaction($booking)
    {
        return Curl::to($this->base_url.'/api/register-transaction')
            ->enableDebug(storage_path('logs/curl.txt'))
            ->withHeader("Api-key: {$this->api_key}")
            ->withHeader('Cache-Control: no-cache')
            ->withData($booking->toArray())
            ->post()
        ;
    }

    public function registerItemTransaction($booking, $type)
    {
        $item_id = $this->getInventoryItemId($type);

        $data = [
            'ref' => $booking->ref,
            'type' => 'out',
            'item_id' => $item_id,
            'quantity' => 1,
            'notes' => '',
            'external' => true,
            'location_id' => $booking->location_id,
        ];

        return $this->post($this->base_url.'/api/register-item-transaction', $data);
    }

    public function connectBoardToInventory($data)
    {
        return $this->post('/api/connect-board-to-inventory', $data);
    }

    public function updateBoardTransaction($data)
    {
        return $this->post('/api/update-board-transaction', $data);
    }

    public function removeBoardTransaction($transaction_id)
    {
        return $this->post('/api/remove-board-transaction', ['transaction_id' => $transaction_id]);
    }

    protected function getInventoryItemId($type)
    {
        $inventory = [
            'kima_bottle' => 29,
            // add more here
        ];

        return $inventory[$type];
    }
}
