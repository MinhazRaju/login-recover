

<?php 



function validation_register_form(){

	$error = [];


	if($_SERVER['REQUEST_METHOD'] == 'POST'){

			$username = $_POST['username'];
			$email    = $_POST['email'];
			$password = $_POST['password'];
			$confirm  = $_POST['confirm_password'];


		if(strlen($username) < 4){
			$error[] = 'Minumun 4 Character';
		}	

		if(empty($email)){

			$error[] = 'Email cannot be empty';
		}	
		if(empty($password)){

			$error[] = 'Password cannot be empty';
		}	

		if($password !== $confirm ){

			$error[] = 'Password not match';
		}


		if(!empty($error)){

		foreach ($error as $value) {
			echo  "<h4 class='alert alert-danger'><strong>Warning ! </strong>$value</b></h4>";
		}

		}
		else{

			register_user($username,$email,$password);

			set_msg("check your mail -  From:webmaster@example.com - spam:check spam folder");

			redirect("index.php");
				
	}		
}







}


function register_user($username,$email,$password){

    global $con;
	$username = escape($username);
	$email	  = escape($email);
	$password = password_hash($password,PASSWORD_BCRYPT,array('cost'=>10));
	$validation = md5($username.microtime());


	if(user_exist($username) || email_exist($email)){
		return false;
	}else{

	$sql = "INSERT INTO user(user_name,user_email,user_password,validation_code,active) VALUES ('$username','$email','$password','$validation',0) "; 

	 $link_send_to_activate_page ="https://pass-recover.000webhostapp.com/activate.php?email=$email&validation_code=$validation";

	$to = $email;
	$subject = "My subject";
	$txt =  $link_send_to_activate_page;
	$headers = "From: webmaster@example.com";
	
	mail($to,$subject,$txt,$headers);


	mysqli_query($con,$sql) or die (mysqli_error($con));

	
	}
	

}




function active_account(){



	if(isset($_GET['email'])){

	$email = $_GET['email'];
	$code  = $_GET['validation_code'];



	$result = query("SELECT id FROM user WHERE user_email = '$email' AND validation_code = '$code'");
	if(row_count($result) == 1){


	query("UPDATE user SET validation_code = 0 , active = 1 WHERE user_email = '$email'");
	set_msg("<p class='bg-success'>Account Active</p>");
	redirect('login.php');



	}


	}


}










function validation_login_form(){


	$error = [];	

	if($_SERVER['REQUEST_METHOD'] == 'POST'){


			$name      = $_POST['username'];
			$password  = $_POST['password'];
			$remember  = isset($_POST['remember']); // Avoid Error if it is no set


			if(empty($name)){

				$error[] = "Enter username";
			}
			if(empty($password)){

				$error[] = "Enter password";
			}


			if(!empty($error)){
			foreach ($error as  $value) {
				echo "<h4 class='alert alert-danger'><strong>Warning ! </strong>$value</b></h4>";
			}
			}else{

				if(login($name,$password,$remember)){
						redirect('admin.php');
					}else{

						echo "Wrong information";
					}
			


			}


	}

}




function login($username,$password,$remember){


		$result = query("SELECT * FROM user WHERE user_name = '$username' AND active = 1 ");

		if(row_count($result) == 1){



			$data = fetch_array($result);
			$db_pass = $data['user_password'];
			

			if(password_verify($password,$db_pass)){

			

				if($remember){
				setcookie('username',$username, time()+84600);

				}

				$_SESSION['username'] = $username;

				return true;

			}else{

				return false;
			}

		return true;




		}else{
		
		
			return false;
		}







}



function logged_in(){


	if(isset($_SESSION['username']) || isset($_COOKIE['username'])){

		return true;
	}else {
		return false;
	}




}


function token_genarator(){

	//token genartaror function genarte uniq id every refresh 

	$token  = $_SESSION['token']  =  md5(uniqid(mt_rand(),true)); 
	return $token;




}


function recover_password(){

   if($_SERVER['REQUEST_METHOD'] == 'POST'){


   	$email = $_POST['email'];
   	$token =  $_POST['token'];// Exisits token 
   	$session_token = $_SESSION['token']; // SESSION TOKEN 


   	if(isset($session_token) && $token === $session_token){
   		// Check send token and exist token same 
  
   $result = query("SELECT user_email FROM user WHERE user_email = '$email'");

   // check databse if  conditon true exectue next statement
  	if(row_count($result) > 0){

  	 $validation = md5($email.microtime());
  	 setcookie('tmp_acess',$validation,time()+240);

  	 //Set cookie and its send to code php page for security purpase


	query("UPDATE user SET validation_code = '$validation' WHERE user_email = '$email' ");

	//update validation code above this set cooke 

	$link_send_to_code_page = "https://pass-recover.000webhostapp.com/code.php?email=$email&validation_code=$validation";
	
	
	
	$to = $email;
	$subject = "Recover code -- $validation";
	$txt =  $link_send_to_code_page;
	$headers = "From: webmaster@example.com";
	
	mail($to,$subject,$txt,$headers);
	redirect('code.php');


  	}

   	else{

   		set_msg("<h4 class='alert alert-danger'><strong>Warning ! </strong>Email doesnot exist</b></h4>");
   		redirect('recover.php');

   	  }

    } 

  }

}	




function recover_validation_code(){

	
	if(isset($_COOKIE['tmp_acess'])){


	if(isset($_POST['code'])){

	$email = $_GET['email'];
	$code  = $_POST['code'];


	$result = query("SELECT id FROM user WHERE user_email = '$email' AND validation_code = '$code' ");
	if(row_count($result) == 1){

		

		redirect('reset.php?email='.$email.'&validation_code='.$code.'');



	}else{

		echo "Wrong validataion code";
	}



	}

	}else{


		set_msg("Validation code expired enter email again");
		redirect('recover.php');
	
	}	

}






function reset_password(){



	if(isset($_COOKIE['tmp_acess'])){

		if(isset($_GET['email']) && isset($_GET['validation_code']) ){

			if(isset($_SESSION['token']) && isset($_POST['token']))

				if($_POST['token'] == $_SESSION['token']){


					if($_SERVER['REQUEST_METHOD']  == 'POST'){


							$password         = $_POST['password'];
							$confirm_password = $_POST['confirm_password'];

						if($password === $confirm_password){

							$password = password_hash($password,PASSWORD_BCRYPT,array('cost'=>12));

						query("UPDATE user SET user_password ='$password' ,validation_code =0 WHERE user_email = '".$_GET['email']."' ");
						redirect('login.php');	

						}else{	

							echo "password not match";
						}	




					}	





		}
	  }	

	}else{

		echo "validataion code expired";
		redirect('recover.php');
	}













}





























 ?>