<?php

namespace App\Jobs;

use App\Jobs\Middleware\TrackImportStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportVehiclesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $vehicleData;
    protected $sourceClass;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($vehicleData, $sourceClass)
    {
        $this->vehicleData = $vehicleData;
        $this->sourceClass = $sourceClass;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $source = new $this->sourceClass;
        $source::saveVehicle($this->vehicleData);

        sleep(5);
    }

    public function middleware()
    {
        return [new TrackImportStatus];
    }
}
