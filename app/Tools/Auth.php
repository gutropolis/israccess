<?php
namespace App\Tools;
use App\Models\User;
use App\Models\Usermeta;
class Auth
{
	
	public function attempt($email, $password) {
		$user = User::where('email', $email)->where('type', '=', 'Admin')->first();
		if(!$user) {
			return false;
		}
		$check_password = $this->checkPasswordIsValid($password,$user->password);
		if($check_password===true) {
			$_SESSION['isAdmin'] = 'Okay';
			$_SESSION['adminId'] = $user->id;
			$_SESSION['adminName'] = $user->name;
			$_SESSION['adminEmail'] = $user->email;

			$profile_picture = $user->user_picture;
			if($profile_picture == '' || $profile_picture === null){
		        $profile_pic_with_path = DEFAULT_PROFILE_IMG;
			 }else{
				$profile_pic_with_path = ADMIN_WEB_PATH.'/thumbs/'.$profile_picture;
			 }
			$_SESSION['profile_pic'] = $profile_pic_with_path;
			
			return true;
		} else {
			return false;
		}
	}
	
	public function checkPasswordIsValid($password,$passwordDb) {
		if (password_verify($password, $passwordDb)) {
			return true;
		} else {
			return false;
		}
	}
	
	// Check user password
	public function checkUserPassword($admin_id, $password){
		$user = User::where('id', $admin_id)->first();
		if(!$user) {
			return false;
		}
		$check_password = $this->checkPasswordIsValid($password,$user->password);
		if($check_password===true) {
			return true;
		}else{
		   return false;	
		}
	 }
	 
	 // Change password
	 public function changePassword($password){
		  return password_hash($password, PASSWORD_BCRYPT);
	 }
	 
	 public	function userattempt($email, $password)
	{
				$user = User::where('email', $email)->first();
				if (!$user)
				{
							return false;
				}
               //$new_password = password_hash($password, PASSWORD_BCRYPT);               // echo 'Password'.$new_password;    exit;
				$check_password = $this->checkPasswordIsValid($password, $user->password);
				if ($check_password === true)
				{
							$_SESSION['isMember'] = 'Okay';
							$_SESSION['memberId'] = $user->id;
							//$_SESSION['memberName'] = $user->name;
							$_SESSION['memberEmail'] = $user->email;
							$_SESSION['memberUname'] = $user->name;
							
							 
							$Usermetas = Usermeta::select('id','first_name','last_name')->where('user_id',$user->id)->first();  
							
							if($Usermetas){
								$_SESSION['memberName'] = $Usermetas->first_name;
							}
							 
							return true;
				}
				else
				{
							return false;
				}
	}
	 
	
	 
	 
	 
	 
}