<?php

namespace Deployer;

set('projections', []);
add('projections', [
    'catalog.moduleList',
    'catalog.public.moduleList',
]);

/**
 * INFO
 * GENERATE NO RESPONSE
 * only 'task composer:audit'
 * maybe insufficient permission from ssh to run the command
 *
 * It works after manual ssh login
 */
desc('Run "php composer audit" on the host.');
task('composer:audit', function () {
    $output = run('cd {{release_or_current_path}} && {{bin/composer}} audit', ['tty' => true]);
    echo $output;
});


/**
 * WARNING
 * GENERATE FAILURE
 * 'exit code 2 (Misuse of shell builtins)'
 * ERROR: Task composer:diagnose failed!
 * insufficient permission from ssh to run the command
 *
 * It works after manual ssh login
 */
desc('Run "php composer diagnose" on the host.');
task('composer:diagnose', function () {
    $output = run('cd {{release_or_current_path}} && {{bin/composer}} diagnose', ['tty' => true]);
    echo $output;
});



desc('Run "bin/console ecotone:es:initialize-projection" on the host.');
task('projection:initialize', function () {
    info('Initialize eventstore projections');

    if (!has('projections')) {
        warning("Please, specify \"projection\" to initialize.");
        return;
    }

    $projections = get('projections');
    foreach ($projections as $projection) {
        info('Current projection: ' . $projection);
        $output = run('cd {{release_or_current_path}} && {{bin/console}} ecotone:es:initialize-projection ' . $projection, ['tty' => true]);
        echo $output;
    }
});
