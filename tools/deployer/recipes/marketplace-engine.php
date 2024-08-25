<?php

namespace Deployer;

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
