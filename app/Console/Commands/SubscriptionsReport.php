<?php

namespace App\Console\Commands;

use Validator;
use Illuminate\Console\Command;

class SubscriptionsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:subscriptions {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the report with the quantity of new, actives and canceled subscription in the day.';

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
     * Show the quantity of 
     *
     * @return mixed
     */
    public function handle()
    {
        $date = $this->argument('date');
        
        $validator = Validator::make(['date'=>$date], [
            'date' => 'required|date_format:Y-m-d'
          ]);

        if ($validator->fails()) {
          $this->error("Error: Invalid date format, must be YYYY-MM-DD.");
        } else {
          $repo = new \App\Repositories\SubscriptionRepository();
          $this->info("Resumen de suscripciones para el día: " . $date);
          $this->line("- Cantidad de nuevas suscripciones: " . $repo->getSubscriptions($date));
          $this->line("- Cantidad de suscripciones canceladas: " . $repo->getUnsubscriptions($date));
          $this->line("- Cantidad de suscripciones activas: " . $repo->getActives($date));
        }
        exit(0);
    }
}
