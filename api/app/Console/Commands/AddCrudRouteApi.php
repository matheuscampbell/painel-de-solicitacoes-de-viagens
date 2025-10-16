<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AddCrudRouteApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:add-crud-route-api {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new CRUD route to api.php';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routePath= base_path('routes/api.php');
        // transforma o nome do model em plural e minusculo com traço ex CanalDeVendas -> canal-de-vendas
        $name = Str::plural(Str::kebab($this->argument('name')));
        $controllrName = Str::ucfirst($this->argument('name')) . 'Controller';
        $routeTemplate = file_get_contents(resource_path('stubs/route.stub'));
        $routeTemplate = str_replace('{{url}}', $name, $routeTemplate);
        $routeTemplate = str_replace('{{controller}}', $controllrName, $routeTemplate);
        file_put_contents($routePath, $routeTemplate, FILE_APPEND);
    }
}
