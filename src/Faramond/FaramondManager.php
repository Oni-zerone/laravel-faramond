<?php

namespace Ennetech\Faramond;

class FaramondManager
{
    public function deploy($branch = null, $verbose = false){
        set_time_limit(0);
        ignore_user_abort(true);

        $git = "git";
        $composer = "composer";

        $root_dir = config('faramond.git-repo-root-path');

        if($branch == null) {
            $branch = config('faramond.git-branch');
        }

        $esit = [];

        $esit[] = $this->execCommand("Activating manteinance mode","cd $root_dir && php artisan down",$verbose);
        $esit[] = $this->execCommand("Removing not-in-repo files","cd $root_dir && $git clean -f",$verbose);
        $esit[] = $this->execCommand("Fetch git refs","cd $root_dir && $git fetch",$verbose);
        $esit[] = $this->execCommand("Resetting repo to default state","cd $root_dir && $git checkout .",$verbose);
        $esit[] =  $this->execCommand("Ensuring we are on the correct branch","cd $root_dir && $git checkout ".$branch,$verbose);
        $esit[] = $this->execCommand("Pulling from upstream","cd $root_dir && $git pull origin ".$branch,$verbose);
        $esit[] = $this->execCommand("Creating composer temp directory","cd $root_dir && mkdir -p composer_temp",$verbose);
        putenv('COMPOSER_HOME='.$root_dir."/composer_temp");
        $esit[] = $this->execCommand("Updating composer","cd $root_dir && $composer update",$verbose);
        $esit[] = $this->execCommand("Removing composer temp directory","cd $root_dir && rm -r composer_temp",$verbose);
        $esit[] = $this->execCommand("Running migrations","cd $root_dir && php artisan migrate",$verbose);
        $esit[] = $this->execCommand("Deactivating manteinance mode","cd $root_dir && php artisan up",$verbose);
        return $esit;
    }

    private function execCommand($description,$command,$verbose = false){
        if($verbose) echo "### $description \n";
        $result = shell_exec($command." 2>&1");
        if($verbose) echo $result;
        if($verbose) echo "\n";
        return [
            "description" => $description,
            "command" => $command,
            "result" => $result
        ];
    }
}
