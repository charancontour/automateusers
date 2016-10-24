<?php namespace Automation\Users;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Dalata\Repositories\EfrontApiInterface;
use App\User;
use App\Branch;
use App\Location;
use Hash;
use DB;
use Queue;
class UserAutomationController extends Controller {
public function __construct(EfrontApiInterface $efront)
{
	$this->efront = $efront;
}
public function branchusers($efront_branch_id)
{
	$branch = Branch::where('efront_branch_id',$efront_branch_id)->first();
	if(empty($branch))
		return "All Bad, Branch Not found in local db.";
	$location = Location::where('location_name',$branch->efront_branch_name)->first();
	if(empty($location))
		return "All Bad, Location Not found in local db.";

	$result = json_decode($this->efront->BranchDetails($efront_branch_id));
	// dd($result);
	if($result->success){
		$insert_array = [];
		$password = Hash::make('Changme1!');
		$timestamp  = date('Y-m-d H:i:s');
		foreach ($result->data->users->list as $user) {
			if($user->active == 1 && !(User::where('login',$user->login)->first())){
				$insert_array[] = [
					'role_id' => 3,
					'efront_user_id'=> $user->id,
					'user_status_id'=>1,
					"branch_id"=>$branch->id,
					'location_id'=>$location->id,
					'login'=>$user->login,
					'firstname'=>$user->name,
					'lastname'=>$user->surname,
					'email'=>$user->email,
					'password'=>$password,
					'created_at'=>$timestamp,
					'updated_at'=>$timestamp,

				];
			}
		}
		$insert_result = DB::table('users')->insert($insert_array);
		if($insert_result)
			return "All Good";
	}

	return "All Bad";

}

public function prefixLogin()
{
$prefix = \Config::get("efront.LoginPrefix");
$users = User::where('login','not like',"$prefix%")
                ->where('user_status_id',1)
                ->get();
foreach($users as $user){
  Queue::push(new AddPrefix($user->id));
}
}
}