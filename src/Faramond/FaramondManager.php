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
