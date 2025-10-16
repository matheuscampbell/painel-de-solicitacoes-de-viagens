<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class newcontroller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:newcontroller {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Controller';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $controllerPath = app_path("Http/Controllers/{$name}Controller.php");

        if (file_exists($controllerPath)) {
            $this->error("O controller {$name} já existe!");
            return;
        }

        $template = file_get_contents(resource_path('stubs/controller.stub'));
        $template = str_replace('{{name}}', $name, $template);

        if (!file_exists(app_path('Http/Controllers'))) {
            mkdir(app_path('Http/Controllers'));
        }

        file_put_contents($controllerPath, $template);

        $this->info("Controller {$name} criado com sucesso!");
    }
}
