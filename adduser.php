<?php
session_start();

//Open a new connection to the MySQL server
$mysqli = new mysqli('sql112.epizy.com', 'epiz_26023437', '68wb2ctr', 'epiz_26023437_EE');

//Output any connection error
if ($mysqli->connect_error) {
    die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$fname = mysqli_real_escape_string($mysqli, $_POST['fname']);
$lname = mysqli_real_escape_string($mysqli, $_POST['lname']);
$email = mysqli_real_escape_string($mysqli, $_POST['email']);
$password = mysqli_real_escape_string($mysqli, $_POST['password']);

//VALIDATION

if (strlen($fname) == 0) {
    echo 'fname';
} elseif (strlen($fname) < 2) {
    echo 'fname';
} elseif (strlen($lname) < 2) {
    echo 'lname';
} elseif (filter_var($lname, FILTER_VALIDATE_EMAIL) === false) {
    echo 'eformat';
} elseif (strlen($email) > 3) {
    echo 'elong';
} elseif ($email > 500) {
    echo 'elong';
} elseif ($email < 1) {
    echo 'elong';
} elseif (strlen($password) <= 4) {
    echo 'pshort';
	
//VALIDATION
	
} else {
	
	//PASSWORD ENCRYPT
	$spassword = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
	
	$query = "SELECT * FROM members WHERE email='$email'";
	$result = mysqli_query($mysqli, $query) or die(mysqli_error());
	$num_row = mysqli_num_rows($result);
	$row = mysqli_fetch_array($result);
	
		if ($num_row < 1) {

			$insert_row = $mysqli->query("INSERT INTO members (fname, lname, email, password) VALUES ('$fname', '$lname', '$email', '$spassword')");

			if ($insert_row) {

				$_SESSION['login'] = $mysqli->insert_id;
				$_SESSION['fname'] = $fname;
				$_SESSION['lname'] = $lname;

				echo 'true';

			}

		} else {

			echo 'false';

		}


	
}

?>