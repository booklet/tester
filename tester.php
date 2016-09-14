<?php
// config data

// ADD AUTOLOADER PATHS
Config::set('autoloader_paths',
    array_merge(
        Config::get('autoloader_paths'),
        ['vendor/tester/src','vendor/tester/src/concerns']
    )
);
