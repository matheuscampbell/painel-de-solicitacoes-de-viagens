<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Api extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API Model, Controller, Request, migration, service and repository';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $this->call('make:model', ['name' => $name, '-m' => true]);
        $this->call('make:newcontroller', ['name' => $name]);
        $this->call('make:request', ['name' => 'Create' . $name . 'Request']);
        $this->call('make:request', ['name' => 'Update' . $name . 'Request']);
        $this->call('make:request', ['name' => 'Get' . $name . 'Request']);
        $this->call('make:request', ['name' => 'Delete' . $name . 'Request']);
        $this->call('make:service', ['name' => $name]);
        $this->call('make:repository', ['name' => $name]);
        $this->call('make:add-crud-route-api', ['name' => $name]);

    }
}
