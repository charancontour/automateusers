<?php namespace Automation\Users;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

use App\User;
use App\Dalata\Repositories\EfrontApiRepository;

class AddPrefix extends Command implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;
	public $user_id;
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($user_id)
	{
		$this->user_id = $user_id;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$user = User::find($this->user_id);
		if($user){
			$efront = new EfrontApiRepository;
			$efront_user_id = $user->efront_user_id;
			$login = \Config::get("efront.LoginPrefix").$user->login;
			$input = ['login'=>$login,'email'=>$user->email,'firstname'=>$user->firstname,'lastname'=>$user->lastname];
			$result = json_decode($efront->EditUser($efront_user_id,$input));
			if($result->success == 'true'){
				$user->login = $login;
				$user->save();
			}
		}
	}

}