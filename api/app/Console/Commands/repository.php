<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class repository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria o arquivo de repository';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $repositoryName = $this->argument('name');
        $repositoryClass = ucfirst($repositoryName);
        $repositoryPath = app_path("Repositories/{$repositoryClass}Repository.php");

        if (file_exists($repositoryPath)) {
            $this->error("O repositório {$repositoryClass} já existe!");
            return;
        }

        $template = file_get_contents(resource_path('stubs/repository.stub'));

        $template = str_replace('{{class}}', $repositoryClass.'Repository', $template);
        $template = str_replace('{{model}}', $repositoryClass, $template);

        if (!file_exists(app_path('Repositories'))) {
            mkdir(app_path('Repositories'));
        }

        file_put_contents($repositoryPath, $template);

        $this->info("Repositório {$repositoryClass} criado com sucesso!");
    }
}
