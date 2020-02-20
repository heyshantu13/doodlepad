<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Post;
use Carbon\Carbon;

class deleteUnpinnedPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Posts After 24 Hours';

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
        Post::where('is_pinned', 0)->whereDate('created_at', '<', Carbon::now()->subHours(24))->delete();
        return "Task Added";
    }
}
