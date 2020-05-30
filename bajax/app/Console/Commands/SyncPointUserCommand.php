<?php
/**
 *
 * PHP version >= 7.0
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */

namespace App\Console\Commands;


use App\User;
use App\ChallengeLog;
use App\Challenge;

use Exception;
use Illuminate\Console\Command;



/**
 * Class deletePostsCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class SyncPointUserCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "sync:point";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Sync Point Users";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $users = User::all();
           
            if (!$users) {
            $this->info("No users exist");
                return;
            }
            foreach ($users as $user) {
                $this->info("ID ".$user->id);
                $realPoint=0;
                $challs=ChallengeLog::where('user_id',$user->id)->get();
                foreach ($challs as $chall) {
                    $realPoint+=Challenge::find($chall->challenge_id)->point;
                }
                $update=$user->update(['point'=>$realPoint]);
                if ($update) 
                    $this->info(" - Success ");
                else
                    $this->error(" - Failed ");
            }
            $this->info("All Point Syncron");
        } catch (Exception $e) {
            $this->error("An error occurred");
        }
    }
}