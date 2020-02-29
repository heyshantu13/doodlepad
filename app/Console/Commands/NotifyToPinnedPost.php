<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NotifyToPinnedPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify User Before Deleting Post';

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
        //
    }
}
