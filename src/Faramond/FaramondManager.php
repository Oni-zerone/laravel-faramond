<?php

namespace Ennetech\Faramond;

class FaramondManager
{
    public function deploy(){
        set_time_limit(0);
        ignore_user_abort(true);

        $git = "git";
        $composer = "composer";

        $root_dir = config('faramond.git-repo-root-path');

        $esit = [];

        $esit[] = $this->execCommand("Activating manteinance mode","cd $root_dir && php artisan down");
        $esit[] = $this->execCommand("Removing not-in-repo files","cd $root_dir && $git clean -f");
        $esit[] = $this->execCommand("Resetting repo to default state","cd $root_dir && $git checkout .");
        $esit[] =  $this->execCommand("Ensuring we are on the correct branch","cd $root_dir && $git checkout ".config('faramond.git-branch'));
        $esit[] = $this->execCommand("Pulling from upstream","cd $root_dir && $git pull origin ".config('faramond.git-branch'));
        putenv('COMPOSER_HOME='.$root_dir);
        $esit[] = $this->execCommand("Updating composer","cd $root_dir && $composer update");
        $esit[] = $this->execCommand("Running migrations","cd $root_dir && php artisan migrate");
        $esit[] = $this->execCommand("Deactivating manteinance mode","cd $root_dir && php artisan up");
        return $esit;
    }

    private function execCommand($description,$command){
        echo "### $description \n";
        $result = shell_exec($command." 2>&1");
        echo $result;
        echo "\n";
        return [
            "description" => $description,
            "command" => $command,
            "result" => $result
        ];
    }
}
