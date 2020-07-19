<!DOCTYPE html>
<html>
<head>
<title>Get Logged in</title>
<link rel='stylesheet' href='../css/bootstrap.css'>
<link rel='stylesheet' href='../css/font-awesome.css'>
<link rel='stylesheet' href='../css/style.css'>
<link rel='stylesheet' href='../css/styly.css'>
<link rel='stylesheet' href='../css/stl.css'>
<script src='../js/jquery.js'></script>
<script src='../js/bootstrap.js'></script>
<script defer src="https://friconix.com/cdn/friconix.js"> </script>
</head>
<body>
<?php include '../includes/nav.php';?>
<?php include 'includes/video-nav.php';?>
<div class='container-fluid' style='background: url(../images/jr3.gif);background-size:100%; height:100vh'>
<form class='register_form col-md-6'> 
<div class='page-header'>
<h2>Sign In <i class="fa fa-sign-in" aria-hidden="true"></i> </h2>
</div>
<div class='form-group'>
<label>Email</label>
<input type='email' class='form-control'>
</div>
<div class='form-group'>
<label>Password</label>
<input type='password' class='form-control'>
</div>
<div class='form-group'>
<button class='btn btn-primary'>Submit</button>
</div>
</form>
</div>
<?php include '../includes/foots.php';?>
</body>
</html>
