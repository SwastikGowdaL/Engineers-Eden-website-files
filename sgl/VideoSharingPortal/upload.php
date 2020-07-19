<!DOCTYPE html>
<html>
<head>
<title>Upload</title>
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
<h2>Upload <i class="fa fa-upload" aria-hidden="true"></i> </h2>
</div>
<div class='form-group'>
<label>Select File</label>
<input type='file' class='form-control'>
</div>
<div class='form-group'>
<label>Title</label>
<input type='text' class='form-control'>
</div>
<div class='form-group'>
<label>Description</label>
<textarea class='form-control'></textarea>
</div>
<div class='form-group'>
<label>Select a Playlist</label>
<select class='form-control'>
<option selected>Playlists</option>
<option>Playlist 1</option>
<option>Playlist 2</option>
<option>Playlist 3</option> 
</select>
</div>
<div class='form-group'>
<button class='btn btn-primary'>Submit</button>
</div>
</form>
</div>
<?php include '../includes/foots.php';?>
</body>
</html>
