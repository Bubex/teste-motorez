<?php

namespace App\Jobs\Middleware;

use App\Events\JobStatusUpdated;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class TrackImportStatus
{
    protected static $firstJobProcessed = false;

    public function handle($job, $next)
    {
        if (!self::$firstJobProcessed) {
            self::$firstJobProcessed = true;
            broadcast(new JobStatusUpdated("Initializing vehicles import..."));
        }

        $next($job);

        sleep(2);

        if (Queue::size() === 1) {
            broadcast(new JobStatusUpdated("Vehicles import finished!"));
            self::$firstJobProcessed = false;
        }
    }
}
