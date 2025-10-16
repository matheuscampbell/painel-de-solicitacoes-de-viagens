<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class service extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria o arquivo de service';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $serviceName = $this->argument('name');
        $serviceClass = ucfirst($serviceName);
        $servicePath = app_path("Services/{$serviceClass}Service.php");

        if (file_exists($servicePath)) {
            $this->error("O serviço {$serviceClass} já existe!");
            return;
        }

        $template = file_get_contents(resource_path('stubs/service.stub'));

        $template = str_replace('{{class}}', $serviceClass.'Service', $template);
        $template = str_replace('{{repository}}', $serviceClass.'Repository', $template);

        if (!file_exists(app_path('Services'))) {
            mkdir(app_path('Services'));
        }

        file_put_contents($servicePath, $template);

        $this->info("Serviço {$serviceClass}Service criado com sucesso!");

    }
}
