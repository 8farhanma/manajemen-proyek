<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ListCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:list-commands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered Artisan commands';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Registered Artisan Commands:');
        
        $commands = Artisan::all();
        
        foreach ($commands as $command) {
            $this->line(sprintf(
                '%s: %s', 
                $command->getName(), 
                $command->getDescription()
            ));
        }

        return 0;
    }
}
