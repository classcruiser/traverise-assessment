<?php

namespace App\Console\Commands;

use App\Models\Classes\ClassMultiPassPayment;
use Illuminate\Console\Command;

class DeleteUnpaidMultiPassOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete_unpaid_multi-pass_orders:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unpaid multi-pass orders after 2 hours delay';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ClassMultiPassPayment::where('created_at', '<', now()->subHours(2))
            ->where('status', '!=', 'CONFIRMED')
            ->whereDoesntHave('records')
            ->delete();

        return Command::SUCCESS;
    }
}
