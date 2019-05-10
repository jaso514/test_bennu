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
     * Execute the console command.
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
          echo "Error: Invalid date format, must be YYYY-MM-DD.";
          exit(0);
        } else {
          $repo = new \App\Repositories\SubscriptionRepository();
          echo $date;
          echo "\n";
          dump($repo->getSubscriptions($date));
          dump($repo->getUnsubscriptions($date));
          dump($repo->getActives($date));
        }
    }
}
