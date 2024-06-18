<?php

namespace App\Actions;

use App\Models\Booking\Guest;

class GenerateGuestIds
{
    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct() {
    }

    public function handle(): void
    {
        $clientId = Guest::whereNotNull('client_id')->orderByDesc('client_id')->value('client_id');
        if (blank($clientId)) {
            $clientId = 0;
        }

        foreach (Guest::whereNull('client_id')->lazy() as $guest) {
            $clientId++;
            $guest->client_id = $clientId;
            $guest->save();
        }
    }
}
