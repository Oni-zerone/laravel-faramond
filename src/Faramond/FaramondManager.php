<?php

namespace Ennetech\Faramond;

use phpDocumentor\Reflection\Types\Boolean;

class FaramondManager
{

    /**
     * @var string
     */
    const git = 'git';

    /**
     * @var string
     */
    const composer = 'composer';

    /**
     * @var string
     */
    private $root_dir;

    /**
     * @var string
     */
    private $branch;

    /**
     * @var bool
     */
    private $require_dev;

    /**
     * @var array
     */
    private $esit;

    function __construct() {

        $this->git = "git";
        $this->composer = "composer";

        $this->branch = config('faramond.git-branch');
        $this->root_dir = config('faramond.git-repo-root-path');

        $this->require_dev = config('app.debug', false);

    }

    /**
     * Launch a deploy procedure.
     * @param null $branch
     * @param bool $verbose
     */
    public function deploy($branch = null, $verbose = false){
        set_time_limit(0);
        ignore_user_abort(true);

        $this->esit = [];

        if($branch == null) {
            $branch = $this->branch;
        }

        $this->esit[] = $this->execCommand("Fetch git refs","cd $this->root_dir && $this->git fetch",$verbose);

        if(!$this->updatesAvailable($branch, $verbose)) {

            $this->esit[] = [
                "description" => "Already up to date.",
                "command" => "",
                "result" => null
            ];

            return $this->completeDeploy();
        }

        $this->esit[] = $this->execCommand("Activating manteinance mode","cd $this->root_dir && php artisan down",$verbose);
        $this->esit[] = $this->execCommand("Removing not-in-repo files","cd $this->root_dir && $this->git clean -f",$verbose);
        $this->esit[] = $this->execCommand("Resetting repo to default state","cd $this->root_dir && $this->git checkout .",$verbose);
        $this->esit[] =  $this->execCommand("Ensuring we are on the correct branch","cd $this->root_dir && $this->git checkout ".$branch,$verbose);
        $this->esit[] = $this->execCommand("Pulling from upstream","cd $this->root_dir && $this->git pull origin ".$branch,$verbose);
        $this->esit[] = $this->execCommand("Creating composer temp directory","cd $this->root_dir && mkdir -p composer_temp",$verbose);
        putenv('COMPOSER_HOME='.$this->root_dir."/composer_temp");
        $no_dev = $this->require_dev ? "" : "--no-dev";
        $this->esit[] = $this->execCommand("Updating composer","cd $this->root_dir && $this->composer install $no_dev",$verbose);
        $this->esit[] = $this->execCommand("Removing composer temp directory","cd $this->root_dir && rm -r composer_temp",$verbose);
        $this->esit[] = $this->execCommand("Running migrations","cd $this->root_dir && php artisan migrate --force",$verbose);
        $this->esit[] = $this->execCommand("Deactivating manteinance mode","cd $this->root_dir && php artisan up",$verbose);

        return $this->completeDeploy();
    }

    /**
     * Execute a shell command.
     * @param $description
     * @param $command
     * @param bool $verbose
     * @return array
     */
    private function execCommand($description,$command,$verbose = false){

        if($verbose) echo "### $description \n";
        $result = shell_exec($command." 2>&1");
        if($verbose) echo $result;
        if($verbose) echo "\n";
        return [
            "description" => $description,
            "command" => $command,
            "result" => trim($result)
        ];
    }

    /**
     * Check if the selected branch last commit is the same of the local HEAD
     * @param $branch
     * @param bool $verbose
     * @return int
     */
    private function updatesAvailable ($branch, $verbose = false) {

        $this->esit[] = $this->execCommand("Get current commit", "cd $this->root_dir && git rev-parse HEAD", $verbose);
        $currentCommit = end($this->esit)['result'];

        $this->esit[] = $this->execCommand("Get reference branch last commit.", "$this->git rev-parse origin/".$branch, $verbose);
        $originCommit = end($this->esit)['result'];

        return strcmp($currentCommit, $originCommit);
    }

    /**
     * Exits from maintenance mode and ends the deploy procedure.
     * @param array $esit
     * @return array
     */
    private function completeDeploy() {

        return $this->esit;
    }
}
