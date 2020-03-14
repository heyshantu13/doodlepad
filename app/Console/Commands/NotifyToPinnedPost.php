<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\PostHelper;


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
       $results = DB::table('posts')->select('id','user_profile_id')
->where('is_pinned',0)
->where('created_at', '<', Carbon::now()->subHours(23)->toDateTimeString())->get();

foreach ($results as $result) {
   PostHelper::createNotifyActivity($result->user_profile_id,$result->id);
}


    }
}
