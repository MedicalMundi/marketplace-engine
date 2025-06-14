<?php
namespace Deployer;

require 'recipe/symfony.php';
require 'tools/deployer/recipes/marketplace-engine.php';

// Config
set('allow_anonymous_stats', false);

set('repository', 'https://github.com/MedicalMundi/marketplace-engine');

set('shared_files', []);
add('shared_dirs', [
    'var/log',
]);

/**
 * Specifies a sub directory within the repository to deploy.
 * Works only when [`update_code_strategy`](#update_code_strategy) is set to `archive` (default).
 *
 * Example:
 *  - set value to `src` if you want to deploy the folder that lives at `/src`.
 *  - set value to `src/api` if you want to deploy the folder that lives at `/src/api`.
 *
 * Note: do not use a leading `/`!
 */
set('sub_directory', 'engine-service');

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
set('application_path_production', 'extensions.openemrmarketplace.com');

/** Production Hosts configuration */
host('production')
    ->setHostname('extensions.openemrmarketplace.com')
    ->set('stage', 'production')
    ->setLabels([
        'env' => 'production',
    ])
    ->set('deploy_path', '~/{{application_path_production}}')
    ->set('http_user', 'urkqihid')
    ->set('writable_use_sudo', false)
    ->set('writable_mode', 'chmod')
    /** ssh settings */
    ->setRemoteUser('urkqihid')
    ->setPort(3508)
    //->set('identityFile', '~/.ssh/id_rsa_marketplace_engine_deployer_local')
    ->set('ssh_multiplexing', false)
    /** git & composer settings */
    ->set('branch', 'main')

    /**
     * In prod composer dovrebbe essere usato con l'opzione --no-dev
     *
     * ->set('composer_options', ' --prefer-dist --no-dev --no-progress --no-interaction --optimize-autoloader')
     */
    ->set('composer_options', ' --prefer-dist --no-progress --no-interaction --optimize-autoloader')
    ->set('keep_releases', 5)
;


/** Staging Application path on hosting server  */
set('application_path_stage', 'stage.extensions.openemrmarketplace.com');

/** Staging Hosts configuration */
host('stage')
    ->setHostname('stage.extensions.openemrmarketplace.com')
    ->set('stage', 'stage')
    ->setLabels([
        'env' => 'stage',
    ])
    ->set('deploy_path', '~/{{application_path_stage}}')
    ->set('http_user', 'urkqihid')
    ->set('writable_use_sudo', false)
    ->set('writable_mode', 'chmod')
    /** ssh settings */
    ->setRemoteUser('urkqihid')
    ->setPort(3508)
    //->set('identityFile', '~/.ssh/id_rsa_marketplace_engine_deployer_local')
    ->set('ssh_multiplexing', false)
    /** git & composer settings */
    ->set('branch', 'main')
    ->set('composer_options', ' --prefer-dist --no-progress --no-interaction --optimize-autoloader')
    ->set('keep_releases', 3)
;


/**
 *  DEPLOYER HOOKS
 */

after('deploy:failed', 'deploy:unlock');
after('deploy', 'envvars:dump');
after('deploy', 'projection:initialize');



/**
 * MAINTENANCE BUNDLE CONFIGURATION
 *
 *  LOCK
 * @see https://packagist.org/packages/corley/maintenance-bundle
 */
desc('Maintenance on');
task('maintenance:on', function () {
    //run('{{bin/console}} corley:maintenance:lock on');
    run('cd {{release_or_current_path}} && {{bin/console}} corley:maintenance:lock on');
    info('Maintenance mode (hard-lock) successfully activated!');
});

desc('Maintenance off');
task('maintenance:off', function () {
    run('{{bin/console}} corley:maintenance:lock off');
    info('Maintenance mode (hard-lock) was deactivated!');
});


/**
 * MAINTENANCE BUNDLE CONFIGURATION
 *
 *  SOFT-LOCK
 * @see https://packagist.org/packages/corley/maintenance-bundle
 */
desc('Maintenance soft-lock on');
task('maintenance:soft:on', function () {
    run('{{bin/console}} corley:maintenance:soft-lock on');
    info('Maintenance mode (soft-lock) successfully activated!');

});

desc('Maintenance soft-lock off');
task('maintenance:soft:off', function () {
    run('{{bin/console}} corley:maintenance:soft-lock off');
    info('Maintenance mode (soft-lock) was deactivated!');
});


desc('Compiles .env files to .env.local.php.');
task('envvars:dump', function () {
    if ('production' === get('labels')['env']){
        /**
         * execute envvars:dump for production
         * when deployer run under
         * developer machine
         */
        if (! getenv('CI')){
            writeln(' labels.env:' . get('labels')['env']);
            info('Setup production env vars in file .env.local.php');
            runLocally('cp -f .env.itroom.production .env.prod');
            info('Generated env.dev with staging configuration data');

            info('Run composer symfony:dump-env prod');
            $cmdResult = runLocally('composer symfony:dump-env prod', ['tty' => true]);
            echo $cmdResult;
            info('Generated .env.local.php');
        }

        /**
         * execute envvars:dump for production
         * when deployer run under
         * CI pipeline (GHA)
         */
        if (getenv('CI')){
            info('GITHUB ACTION - Create and populate .env.dev file for production');
            $cmdResult = runLocally('ls -al');
            echo $cmdResult;

            info('Remove generic .env ');
            $cmdResult = runLocally('rm -f .env');
            echo $cmdResult;

            info('Generated env with production configuration data');
            $cmdResult = runLocally('touch .env');
            echo $cmdResult;


            $APP_ENV = getenv('APP_ENV');
            runLocally("echo APP_ENV=\"$APP_ENV\" >> .env");

            $APP_SECRET = getenv('APP_SECRET');
            runLocally("echo APP_SECRET=\"$APP_SECRET\" >> .env");

            $DATABASE_URL = getenv('DATABASE_URL');
            runLocally("echo DATABASE_URL=\"$DATABASE_URL\" >> .env");

            $LOCK_DSN = getenv('LOCK_DSN');
            runLocally("echo LOCK_DSN=\"$LOCK_DSN\" >> .env");

            $MAILER_DSN = getenv('MAILER_DSN');
            runLocally("echo MAILER_DSN=\"$MAILER_DSN\" >> .env");

            $OAUTH_GITHUB_CLIENT_ID = getenv('OAUTH_GITHUB_CLIENT_ID');
            runLocally("echo OAUTH_GITHUB_CLIENT_ID=\"$OAUTH_GITHUB_CLIENT_ID\" >> .env");

            $OAUTH_GITHUB_CLIENT_SECRET = getenv('OAUTH_GITHUB_CLIENT_SECRET');
            runLocally("echo OAUTH_GITHUB_CLIENT_SECRET=\"$OAUTH_GITHUB_CLIENT_SECRET\" >> .env");

            $OAUTH_OEMODULES_CLIENT_ID = getenv('OAUTH_OEMODULES_CLIENT_ID');
            runLocally("echo OAUTH_OEMODULES_CLIENT_ID=\"$OAUTH_OEMODULES_CLIENT_ID\" >> .env");

            $OAUTH_OEMODULES_CLIENT_SECRET = getenv('OAUTH_OEMODULES_CLIENT_SECRET');
            runLocally("echo OAUTH_OEMODULES_CLIENT_SECRET=\"$OAUTH_OEMODULES_CLIENT_SECRET\" >> .env");

            $SENTRY_DSN = getenv('SENTRY_DSN');
            runLocally("echo SENTRY_DSN=\"$SENTRY_DSN\" >> .env");
            $cmdResult = runLocally('cat .env');
            echo $cmdResult;

            info('Run composer symfony:dump-env prod');
            $cmdResult = runLocally('composer symfony:dump-env prod', ['tty' => true]);
            echo $cmdResult;
            info('Generated .env.local.php');


            $cmdResult = runLocally('cat .env.local.php');
            echo $cmdResult;
        }

    }elseif ('stage' === get('labels')['env']){

        if (! getenv('CI')){
            info('Setup stage env vars in file .env.local.php');
            runLocally('cp -f .env.itroom.stage .env.dev');
            info('Generated env.dev with staging configuration data');

            info('Run composer symfony:dump-env dev');
            $cmdResult = runLocally('composer symfony:dump-env dev', ['tty' => true]);
            echo $cmdResult;
            info('Generated .env.local.php');
        }

        if (getenv('CI')){
            info('GITHUB ACTION - Create and populate .env.dev file for stage');
            $cmdResult = runLocally('ls -al');
            echo $cmdResult;

            info('Remove generic .env.dev ');
            $cmdResult = runLocally('rm -f .env.dev');
            echo $cmdResult;

            info('Generated env.dev with staging configuration data');
            $cmdResult = runLocally('touch .env.dev');
            echo $cmdResult;


            $APP_ENV = getenv('APP_ENV');
            runLocally("echo APP_ENV=\"$APP_ENV\" >> .env.dev");

            $APP_SECRET = getenv('APP_SECRET');
            runLocally("echo APP_SECRET=\"$APP_SECRET\" >> .env.dev");

            $DATABASE_URL = getenv('DATABASE_URL');
            runLocally("echo DATABASE_URL=\"$DATABASE_URL\" >> .env.dev");

            $LOCK_DSN = getenv('LOCK_DSN');
            runLocally("echo LOCK_DSN=\"$LOCK_DSN\" >> .env.dev");

            $MAILER_DSN = getenv('MAILER_DSN');
            runLocally("echo MAILER_DSN=\"$MAILER_DSN\" >> .env.dev");

            $OAUTH_GITHUB_CLIENT_ID = getenv('OAUTH_GITHUB_CLIENT_ID');
            runLocally("echo OAUTH_GITHUB_CLIENT_ID=\"$OAUTH_GITHUB_CLIENT_ID\" >> .env.dev");

            $OAUTH_GITHUB_CLIENT_SECRET = getenv('OAUTH_GITHUB_CLIENT_SECRET');
            runLocally("echo OAUTH_GITHUB_CLIENT_SECRET=\"$OAUTH_GITHUB_CLIENT_SECRET\" >> .env.dev");

            $OAUTH_OEMODULES_CLIENT_ID = getenv('OAUTH_OEMODULES_CLIENT_ID');
            runLocally("echo OAUTH_OEMODULES_CLIENT_ID=\"$OAUTH_OEMODULES_CLIENT_ID\" >> .env.dev");

            $OAUTH_OEMODULES_CLIENT_SECRET = getenv('OAUTH_OEMODULES_CLIENT_SECRET');
            runLocally("echo OAUTH_OEMODULES_CLIENT_SECRET=\"$OAUTH_OEMODULES_CLIENT_SECRET\" >> .env.dev");

            $cmdResult = runLocally('cat .env.dev');
            echo $cmdResult;

            info('Run composer symfony:dump-env dev');
            $cmdResult = runLocally('composer symfony:dump-env dev', ['tty' => true]);
            echo $cmdResult;
            info('Generated .env.local.php');


            $cmdResult = runLocally('cat .env.local.php');
            echo $cmdResult;
        }

//        info('Run composer symfony:dump-env dev');
//        $cmdResult = runLocally('composer symfony:dump-env dev', ['tty' => true]);
//        echo $cmdResult;
//        info('Generated .env.local.php');
    }

    info('Try to upload .env.local.php');
    upload(__DIR__.'/.env.local.php', '{{release_path}}/.env.local.php');
    info('Success: uploaded .env.local.php');

    info('Cleanup local directories');
    runLocally('rm -f .env.dev');
    runLocally('rm -f .env.prod');
    info('Remove generated .env.xxxx files from local filesystem');

    runLocally('rm -f .env.local.php');
    info('Remove generated .env.local.php from local filesystem');
});