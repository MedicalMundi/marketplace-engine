<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config
set('allow_anonymous_stats', false);

set('repository', 'https://github.com/MedicalMundi/marketplace-engine');

add('shared_files', []);
add('shared_dirs', [
    'var/log',
]);
add('writable_dirs', []);
// Writable dirs by web server
add('writable_dirs', [
    'var',
]);


/**
 *
 * HOSTS CONFIGURATION ( production & stage )
 *
 */


/** Production Application path on hosting server  */
set('application_path_production', 'marketplace.oe-modules.com');

/** Production Hosts configuration */
host('production')
    ->setHostname('marketplace.oe-modules.com')
    ->set('stage', 'production')
    ->set('deploy_path', '~/{{application_path_production}}')
    ->set('http_user', 'ekvwxsme')
    ->set('writable_use_sudo', false)
    ->set('writable_mode', 'chmod')
    /** ssh settings */
    ->setRemoteUser('ekvwxsme')
    ->setPort(3508)
    ->set('identityFile', '~/.ssh/id_rsa_oe_modules_php_deployer')
    ->set('ssh_multiplexing', false)
    /** git & composer settings */
    ->set('branch', 'main')
    ->set('composer_options', ' --prefer-dist --no-progress --no-interaction --optimize-autoloader')
    ->set('keep_releases', 5)
;


/** Staging Application path on hosting server  */
set('application_path_stage', 'stage.marketplace.oe-modules.com');

/** Staging Hosts configuration */
host('stage')
    ->setHostname('stage.marketplace.oe-modules.com')
    ->set('stage', 'stage')
    ->set('deploy_path', '~/{{application_path_stage}}')
    ->set('http_user', 'ekvwxsme')
    ->set('writable_use_sudo', false)
    ->set('writable_mode', 'chmod')
    /** ssh settings */
    ->setRemoteUser('ekvwxsme')
    ->setPort(3508)
    ->set('identityFile', '~/.ssh/id_rsa_oe_modules_php_deployer')
    ->set('ssh_multiplexing', false)
    /** git & composer settings */
    ->set('branch', 'main')
    ->set('composer_options', ' --prefer-dist --no-progress --no-interaction --optimize-autoloader')
    ->set('keep_releases', 2)
;


/**
 *  DEPLOYER HOOKS
 */

after('deploy:failed', 'deploy:unlock');


/**
 * MAINTENANCE BUNDLE CONFIGURATION
 */
desc('Maintenance on');
task('maintenance:on', function () {
    run('{{bin/php}} {{bin/console}} corley:maintenance:soft-lock on');
});

desc('Maintenance off');
task('maintenance:off', function () {
    run('{{bin/php}} {{bin/console}} corley:maintenance:soft-lock off');
});
