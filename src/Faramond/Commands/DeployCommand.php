<?php

namespace Ennetech\Faramond\Commands;

use App\Jobs\SendReservationToVehicle;
use App\Models\Reservation;
use Carbon\Carbon;
use Ennetech\Faramond\FaramondManager;
use Illuminate\Console\Command;

class DeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'faramond:deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy the app from VCS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new FaramondManager())->deploy(null,true);
    }
}
