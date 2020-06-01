<?php

namespace App\Console\Commands;

use App\Libraries\DeliveryPlatformQueue\queue;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class DeliveryPlatformQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DeliveryPlatformQueue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Queue from Delivery Platform and after parsing complete the order cycle.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        print $this->description ."\n\n";
        $queue_obj = new queue();
        $queue_obj->getOrderQueue();
        $queue_obj->operationQueue();
        $queue_obj->removeQueueItem();
    }
}
