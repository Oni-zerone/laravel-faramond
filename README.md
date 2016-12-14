# Laravel Faramond

## Disclaimer
I am publishing much, much, (much) earlier than when I should, so expect some issues.
You are free to do whatever you want with this, but remember that is in ALPHA stage and offered "as is".

## Introduction
*FARAMOND: Later spelling of Old High German Faramund, meaning "journey protection."*

Faramond aims to be your best friend when deploy ~~and/or remote control~~ is involved. 

## Requirement of target server
- git
- composer
- rsa keypair configured for the web-server user if the source repository is private
    
## How to check if the server is correctly configured?
After loggin in as www-data (```sudo su -s /bin/bash www-data```) and navigating to ```$git-repo-root-path``` directory verify that none of this commands (in this order) gives error or requires additional user interaction:
```
php artisan down
git clean -f
git fetch
git checkout .
git checkout $branch_name
git pull origin $branch_name
mkdir -p composer_temp
composer update
rm -r composer_temp
php artisan migrate
php artisan up
```

## Installation
Install the package via composer:

``` bash
composer require enne/laravel-faramond
```

Add the provider to the 'providers' array in config/app.php
```php
'providers' => [
    ...
    Ennetech\Faramond\FaramondServiceProvider::class,
    ...
],
```

[NOT INCLUDED ATM, just ignore this] ~~Add the facade to the 'aliases' array in config/app.php~~
```php
'aliases' => [
    ...
    'Faramond' => Ennetech\Faramond\Facades\Faramond::class,
    ...
],
```

Publish the config with:
```bash
php artisan vendor:publish --provider="Ennetech\Faramond\FaramondServiceProvider" --tag="config"
```

Edit the config to match your envrioment
```php
return [
    // Application version
    'version' => [
        // Current git branch
        "branch" => exec('git rev-parse --abbrev-ref HEAD'),
        // Current git commit
        "commit" => exec('git rev-parse HEAD')
    ],
    // Prefix for faramond routes
    'route-prefix' => 'faramond',
    // Repository ABSOLUTE root path in the server
    'git-repo-root-path' => base_path(),
    // Source branch
    'git-branch' => 'faramond'
    // Key to protect webhook route
    'secret' => 'change_me_please'
];
```
## Commands
Faramond will expose those commands to the artisan cli tool

##### faramond:deploy
Deploy the app from VCS on default branch

## APIs
Faramond will expose those APIs to YOUR-APP/route-prefix

##### GET /version/>SECRET<
Will return the configured version value

##### POST /update/>SECRET<
This will trigger the deploy procedure and return a json with a detail of each operation executed

## Coming soon (hopefully):
- Logging to a remote server

## License
MIT

