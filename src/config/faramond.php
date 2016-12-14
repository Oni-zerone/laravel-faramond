<?php

return [
    // Application version
    'version' => [
        "branch" => exec('git rev-parse --abbrev-ref HEAD'),
        "commit" => exec('git rev-parse HEAD')
    ],
    // Prefix for faramond routes
    'route-prefix' => 'faramond',
    // Repository ABSOLUTE root path in the server
    'git-repo-root-path' => base_path(),
    // Source branch
    'git-branch' => 'faramond',
    // Key to protect webhooks route
    'secret' => 'change_me_please'
];