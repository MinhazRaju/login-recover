<?php 



	$db['db_host']    = 'localhost';
	$db['db_user']    = 'id8932280_admin';
	$db['db_pass'] 	  = '123456789';
	$db['db_name']	  = 'id8932280_login';


	foreach ($db as $key => $value) {
		 define(strtoupper($key),$value);
	}

	$con = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

	if($con){

		echo "";
	}



function redirect($location){
	return  header("location:$location");
}



function set_msg($msg){

	return $_SESSION['msg'] = $msg;
}


function display_msg(){

	if(isset($_SESSION['msg'])){
		echo $_SESSION['msg'];
		unset($_SESSION['msg']);
}
}



function escape($string){

	global $con;
	return mysqli_real_escape_string($con,$string);
}

function query($sql){

	global $con;
	$query =  mysqli_query($con,$sql) or die(mysqli_error($con));
	return $query;
}



function fetch_array($query){

	global $con;
	return mysqli_fetch_array($query);
}


function row_count($query){

global $con;
return mysqli_num_rows($query);

}



function user_exist($username){
  
	$result =  query("SELECT id FROM user WHERE user_name = '$username'");
	if(row_count($result) > 0 ){

	echo "<h3 class='text-center alert-danger'>Try Diffrent User Name</h3>";
	return true;
	}else{

	return false;
	}

}


function email_exist($email){

	$result = query("SELECT id FROM user WHERE user_email = '$email'");
	if(row_count($result) > 0){

		echo "<h3 class='text-center alert-danger'>Email Adress Exists</h3>";
		return true;
	}else{
		return false;
	}
}







 ?>