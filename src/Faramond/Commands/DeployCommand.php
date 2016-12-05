<?php

namespace Ennetech\Faramond\Commands;

use App\Jobs\SendReservationToVehicle;
use App\Models\Reservation;
use Carbon\Carbon;
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
        set_time_limit(0);
        ignore_user_abort(true);

        $git = "git";
        $composer = "composer";

        $root_dir = config('faramond.git-repo-root-path');

        $this->execCommand("Activating manteinance mode","cd $root_dir && php artisan down");
        $this->execCommand("Removing not-in-repo files","cd $root_dir && $git clean -f");
        $this->execCommand("Resetting repo to default state","cd $root_dir && $git checkout .");
        $this->execCommand("Ensuring we are on the correct branch","cd $root_dir && $git checkout ".config('faramond.git-branch'));
        $this->execCommand("Pulling from upstream","cd $root_dir && $git pull origin ".config('faramond.git-branch'));
        putenv('COMPOSER_HOME='.$root_dir);
        $this->execCommand("Updating composer","cd $root_dir && $composer update");
        $this->execCommand("Running migrations","cd $root_dir && php artisan migrate");
        $this->execCommand("Deactivating manteinance mode","cd $root_dir && php artisan up");
    }

    private function execCommand($description,$command){
        echo "### $description \n";
        echo shell_exec($command." 2>&1");
        echo "\n";
    }
}
