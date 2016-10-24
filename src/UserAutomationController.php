<?php namespace Automateusers;
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
	$admin_id = 2;
	$student_id = 3;
	$password = Hash::make("Changeme1!");
	$branches = branch::where('efront_branch_id',$efront_branch_id)->get();
	if(empty($branches)){
		return "branch not found in the DB.";
	}
	foreach ($branches as $branch) {
		$location = Location::where('location_name',$branch->efront_branch_name)->first();
		if(empty($location)){
			return "location with branch name not found,Please make sure location is also there";
		}
		$branch_result = json_decode($this->efront->BranchDetails($branch->efront_branch_id));
		// dd($branch_result);
		if($branch_result->success){
				foreach ($branch_result->data->users->list as $efront_user) {
					$efront_user_role_id = $student_id;
					if(intval($efront_user->user_types_ID) === 9){
						$efront_user_role_id = $admin_id;
					}
					// dd($efront_user_role_id);
					$user = User::create(['efront_user_id'=>$efront_user->id,
																'firstname'=>$efront_user->name,
																'lastname'=>$efront_user->surname,
																'email'=>$efront_user->email,
																'login'=>$efront_user->login,
																'user_status_id'=>1,
																'role_id'=>$efront_user_role_id,
																'location_id'=>$location->id,
																'branch_id'=>$branch->id,
																'password'=>$password]);

					if(intval($efront_user->active) != 1){
						$user->delete();
					}
				}
		}
		else{
			return "efront error, check efront DB wether efront id is present";
		}
	}
	return "All Good";
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
