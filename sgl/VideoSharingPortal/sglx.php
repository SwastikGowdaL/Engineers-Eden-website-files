<?php
//Initialize Session
session_start();

if (isset($_SESSION['login'])) {

    $fname = $_SESSION['fname'];
    $lname = $_SESSION['lname'];

?>

<!DOCTYPE html>
<a href="https://icons8.com/icon/wFfu6zXx15Yk/home"></a>
<html>
<head>
<title>SGLX</title>
<link rel='stylesheet' href='../css/bootstrap.css'>
<link rel='stylesheet' href='../css/font-awesome.css'>
<link rel='stylesheet' href='../css/style.css'>
<link rel='stylesheet' href='../css/styly.css'>

<meta name="viewport" content="width=device-width, initial-scale=1"/>


<link rel="shortcut icon" 
      type="image/x-icon" 
      href="../../favicon.ico">
<script src='../js/jquery.js'></script>
<script src='../js/bootstrap.js'></script>
</head> 
<body>
<?php include '../includes/nav.php';?>
<?php include 'includes/video-nav.php';?>
	<div class='container-fluid'>
	<div class='row'> 
	<div class='col-md-2'>
	<div class='page-header' style='margin-top: 0;'>
	<h4>Categories <i class="fa fa-th" aria-hidden="true"></i> </h4>
	</div>
	<div class='vid-sidebar-list'> 
	<a href='../../index.php' class='vid-sidebar-list-item'><img src="https://img.icons8.com/fluent/24/000000/home.png"/> Main Home</a>
	<a href='sglx.php' class='vid-sidebar-list-item'><span class="fa fa-home"></span> Home</a>
	<a href='#' class='vid-sidebar-list-item'><i class="fa fa-file"></i> Software</a>
	<a href='#' class='vid-sidebar-list-item'><i class="fa fa-cogs"></i> Technology</a>
	<a href='#' class='vid-sidebar-list-item'><i class="fa fa-medkit"></i> Health</a>
	<a href='#' class='vid-sidebar-list-item'><i class="fa fa-flask"></i> Science</a>
	</div>
	<div class='page-header' style='margin-top: 20px;'>
	<h4>My Account <i class="fa fa-user" aria-hidden="true"></i> </h4>
	</div>
	<div class='vid-sidebar-list'>
	<a href='#' class='vid-sidebar-list-item'><i class="fa fa-desktop"></i> My Channels</a>
	<a href='#' class='vid-sidebar-list-item'><i class="fa fa-list"></i> My Playlists</a>
	<a href='#' class='vid-sidebar-list-item'><i class="fa fa-thumbs-up"></i> My Liked Videos</a>
	<a href='#' class='vid-sidebar-list-item'><i class="fa fa-plus-square"></i> My Subscribed channels</a>
	</div>
	</div>
	<div class='col-md-10'>
	<div class='row'>
	<div class='page-header' style='margin-top:0;'>
	<p style='color: red'>note that this site is a beta version and is still under construction we are trying to make it better day by day but it takes a lot of knowledge,time,expenses and manpower which we don't have but still we won't give up. For better user interface view it on PC.</p>
	
	
	<div class='row'>
	<div class='page-header'>
    <h3>...New Videos... <i class="fa fa-spinner fa-pulse"></i> </h3>
    </div>	
	<div class='col-md-3'>
	<div class='New-videos'>
	<a href='player2.php' class='play-button'></a>
	<img src='../images/s2.jpg' class='img-responsive'>
	<div class='new-video-details'>
	<h4>Top 10 technologies</h4>
	<div class='new-video-author'>by: <strong>TechSwastik</strong></div>
	<div class='new-video-description'>Here,we're looking at the top 10 technologies</div>
    <br>
	</div>
	</div>
	</div>
<div class='col-md-3'>
	<div class='New-videos'>
	<a href='player.php' class='play-button'></a>
	<img src='../images/s1.jpg' class='img-responsive'>
	<div class='new-video-details'>
	<h4>Top programming Languages</h4>
	<div class='new-video-author'>by: <strong>TechSwastik</strong></div>
	<div class='new-video-description'>Here,we're looking at top programming languages.</div>
	</div>
	</div>
	</div>
	<div class='col-md-3'>
	<div class='New-videos'>
	<a href='player3.php' class='play-button'></a>
	<img src='../images/v1.jpg' class='img-responsive'>
	<div class='new-video-details'>
	<h4>DDoS</h4>
	<div class='new-video-author'>by: <strong>TechSwastik</strong></div>
	<div class='new-video-description'>Here,we're will learn what is DDoS attack.</div>
	</div>
	</div>
	</div>
	<div class='col-md-3'>
	<div class='New-videos'>
	<a href='player4.php' class='play-button'></a>
	<img src='../images/u1.jpg' class='img-responsive'>
	<div class='new-video-details'>
	<h4>INTEL Processor Comparison</h4>
	<div class='new-video-author'>by: <strong>UJVAL Knowledge Jar</strong></div>
	<div class='new-video-description'>Here,we're looking at the comparison between the processors</div>
	</div>
	</div>
	</div>
	</div>
	<div class='row'>
	<div class='page-header'>
	<h3>...Trending Videos... <i class="fa fa-line-chart" aria-hidden="true"></i></h3>
	</div>
	<div class='col-md-3'>
	<div class='New-videos'>
	<a href='#' class='play-button'></a>
	<img src='../images/i5.jpg' class='img-responsive'>
	<div class='new-video-details'>
	<h4>GEARS</h4>
	<div class='new-video-author'>by: <strong>TechDragon</strong></div>
	<div class='new-video-description'>Here,we're looking at the history of gears</div>
	</div>
	</div>
	</div>
	<div class='col-md-3'>
	<div class='New-videos'>
	<a href='#' class='play-button'></a>
	<img src='../images/i5.jpg' class='img-responsive'>
	<div class='new-video-details'>
	<h4>GEARS</h4>
	<div class='new-video-author'>by: <strong>TechDragon</strong></div>
	<div class='new-video-description'>Here,we're looking at the history of gears</div>
	</div>
	</div>
	</div>
	<div class='col-md-3'>
	<div class='New-videos'>
	<a href='#' class='play-button'></a>
	<img src='../images/i5.jpg' class='img-responsive'>
	<div class='new-video-details'>
	<h4>GEARS</h4>
	<div class='new-video-author'>by: <strong>TechDragon</strong></div>
	<div class='new-video-description'>Here,we're looking at the history of gears</div>
	</div>
	</div>
	</div>
	<div class='col-md-3'>
	<div class='New-videos'>
	<a href='#' class='play-button'></a>
	<img src='../images/i5.jpg' class='img-responsive'>
	<div class='new-video-details'>
	<h4>GEARS</h4>
	<div class='new-video-author'>by: <strong>TechDragon</strong></div>
	<div class='new-video-description'>Here,we're looking at the history of gears</div>
	</div>
	</div>
	</div>
	</div>
 <?php include"../includes/footer.php";?>
</body> 
</html>

<?php

} else {
    header("location:../../siteclosed/index.php ");
}
?>