<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'ta23-hajus');

// Project repository
set('repository', 'git@github.com:Anne-dot/ta23-hajus.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', ['.env']);
add('shared_dirs', [
    'storage',
    'bootstrap/cache',
]);

// Writable dirs by web server 
add('writable_dirs', [
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
]);

// Set composer options
set('composer_options', '--no-dev --optimize-autoloader');

// Set the web server user
set('http_user', 'virt123071');

// Keep last 5 releases
set('keep_releases', 5);

// Hosts - Using your actual Zone.ee details
host('zone.ee')
    ->setHostname('ta23ruusmann.itmajakas.ee')
    ->setRemoteUser('virt123071')
    ->setDeployPath('/data01/virt123071/domeenid/www.ta23ruusmann.itmajakas.ee/hajus-ta23-deployer')
    ->setIdentityFile('~/.ssh/id_ed25519');

// Tasks
task('build', function () {
    cd('{{release_path}}');
    run('npm install');
    run('npm run build');
})->desc('Build frontend assets');

// Clear all Laravel caches
task('artisan:cache:clear', function () {
    run('{{bin/php}} {{release_path}}/artisan cache:clear');
    run('{{bin/php}} {{release_path}}/artisan config:clear');
    run('{{bin/php}} {{release_path}}/artisan route:clear');
    run('{{bin/php}} {{release_path}}/artisan view:clear');
})->desc('Clear all Laravel caches');

// Run build task before deploying
before('deploy:symlink', 'build');

// Clear caches after deployment
after('deploy:symlink', 'artisan:cache:clear');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'artisan:migrate');