# Laravel Faramond

## Disclaimer
I am publishing much much (much) earlier than when I should, so expect some problems.
You are free to do whatever you want with this, but remember that is in ALPHA stage and offered "as is".

## Introduction
*FARAMOND: Later spelling of Old High German Faramund, meaning "journey protection."*

Faramond aims to be your best friend when deploy and/or remote control is involved. 

## Requirement of target server
- git
- composer
- rsa keypair configured for the web-server user if the source repository is private    

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

[NOT INCLUDED ATM, just ignore this] Add the facade to the 'aliases' array in config/app.php
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
    'version' => '0.3',
    // Prefix for faramond routes
    'route-prefix' => 'faramond',
    // Repository ABSOLUTE root path in the server
    'git-repo-root-path' => base_path(),
    // Repository path
    'git-repo' => 'ssh://git@github.com:neurogas/laravel-faramond.git',
    // Source branch
    'git-branch' => 'faramond'
];
```
## Commands
Faramond will expose those commands to the artisan cli tool

##### faramond:deploy
Deploy the app from VCS

## APIs
Faramond will expose those APIs to YOUR-APP/route-prefix

##### /version
Will return the configured version value

## Coming soon (hopefully):
- (Secure) Remote deploy hook
- Logging to a remote server

## License
MIT

