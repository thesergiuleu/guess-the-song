<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProjectInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app_rebuild';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run everything you need to initialize the project';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment('COMPOSER INSTALL');
        exec('composer install');
        $this->comment('COMPOSER DUMPAUTOLOAD');
        exec('composer dumpautoload');
        $this->comment('PHP ARTISAN KEY:GENERATE');
        $this->call('php artisan key:generate');
        $this->comment('PHP ARTISAN STORAGE:LINK');
        $this->call('php artisan storage:link');
        $this->comment('PHP ARTISAN MIGRATE');
        $this->call('php artisan migrate');
        $this->comment('PHP ARTISAN PASSPORT:INSTALL');
        $this->call('php artisan passport:install');
    }
}
