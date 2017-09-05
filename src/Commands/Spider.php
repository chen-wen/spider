<?php 
namespace Wayne\Spider\Commands;

use Illuminate\Console\Command;
use Wayne\Spider\SpiderManager;

class Spider extends Command 
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawl content from website';

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
        if ($this->hasArgument('name')) {
            $name = $this->Argument('name');
            SpiderManager::handle($name);
        } else {
            SpiderManager::handle();
        }
    }

    public function getArguments()
    {
        return [
            [
                'name',
                InputArgument::OPTIONAL,
                'The name of spider.',
                null
            ],
        ];
    }

}
