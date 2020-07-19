<?php
//Initialize Session
session_start();

if (isset($_SESSION['login'])) {

    $fname = $_SESSION['fname'];
    $lname = $_SESSION['lname'];

?>

<!DOCTYPE html>
<a href="https://icons8.com/icon/wFfu6zXx15Yk/home"></a>
<script defer src="https://friconix.com/cdn/friconix.js"> </script>
<html lang="en">

<head>


 
<script data-ad-client="ca-pub-8720961540537957" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
      
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Engineers Eden - Home</title>

  
    
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/business-casual.css" rel="stylesheet">
    <link rel="icon" href="EE.png" type="image/png">
    <!-- Fonts -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>


    <div class="brand"><img src="https://img.icons8.com/cute-clipart/64/000000/machine-learning.png"/> Engineers Eden</div>
    <div class="address-bar">Banglore | Karnataka | India</div>
 
	<?php
		
	if (isset($_GET["logout"])) {
		
		if ($_GET["logout"] == "true") { ?>
			
			<div class="alert alert-success">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong>You have been logged out of the system.</strong>
			</div>   

	<?php
		}
	}
	?>

	
    <!-- Navigation -->
    <?php require_once 'nav.php'; ?>

    <div class="container">

        <div class="row">
            <div class="box">
                <div class="col-lg-12 text-center">
                    <div id="carousel-example-generic" class="carousel slide">
                        <!-- Indicators -->
                        <ol class="carousel-indicators hidden-xs">
                            <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="2"></li>
							<li data-target="#carousel-example-generic" data-slide-to="3"></li>
							<li data-target="#carousel-example-generic" data-slide-to="4"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="5"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="6"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="7"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="8"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="9"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="10"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="11"></li>
                        </ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner">
                            <div class="item active">
                                <img class="img-responsive img-full" src="img/a1.jpg" alt="">
                            </div>
                            <div class="item">
                                <img class="img-responsive img-full" src="img/a3.jpg" alt="">
                            </div>
                            <div class="item">
                                <img class="img-responsive img-full" src="img/class.jpg" alt="">
                            </div>
                            <div class="item">
                                <img class="img-responsive img-full" src="img/a6.jpg" alt="">
                            </div>
							<div class="item">
                                <img class="img-responsive img-full" src="img/a7.jpg" alt="">
                            </div>
							<div class="item">
                                <img class="img-responsive img-full" src="img/a4.jpg" alt="">
                            </div>
                            <div class="item">
                                <img class="img-responsive img-full" src="img/udemy.jpg" alt="">
                            </div>
                            <div class="item">
                                <img class="img-responsive img-full" src="img/vg1.jpg" alt="">
                            </div>
                            <div class="item">
                                <img class="img-responsive img-full" src="img/raspb.jpg" alt="">
                            </div>
                            <div class="item">
                                <img class="img-responsive img-full" src="img/lifi2.jpg" alt="">
                            </div>
                            <div class="item">
                                <img class="img-responsive img-full" src="img/tel.jpg" alt="">
                            </div>
                            <div class="item">
                                <img class="img-responsive img-full" src="img/BChain.jpg" alt="">
                            </div>                      
                        </div>

                        <!-- Controls -->
                        <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                            <span class="icon-prev"></span>
                        </a>
                        <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                            <span class="icon-next"></span>
                        </a>
                    </div>
                    <h2 class="brand-before">
                        <small>Welcome to</small>
                    </h2>
					<img src="https://img.icons8.com/cute-clipart/64/000000/machine-learning.png"/>
                    <h1 class="brand-name">Engineers Eden</h1>
                    <hr class="tagline-divider">
                    <h2>
                        <small>By
                            <strong>Swastik and Team.</strong>
                        </small>
                    </h2>
                </div>
            </div>
        </div>
		
    <div class="row">
            <div class="box">
                <div class="col-lg-12">
                    <hr>
                    <h2 class="intro-text text-center">Beleive Me this site is
                        <strong>worth visiting</strong>
                    </h2>
                    <hr>
                    <img class="img-responsive img-border img-left" src="img/a8.jpg" alt="">
                    <hr class="visible-xs">
                    <p>You might ask why is it worth visiting? Believe me its worth it because we will be uploading various valuable contents and info which are pretty hard to find for you guys out there. We on the other hand, through various means and contacts collect them and share it here. So I say visit this site every now and then for various useful and interesting contents. The content includes info blogs, ebooks, tutorials, etc.., of various fields of C.S engineering along other stream of courses. </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="box">
                <div class="col-lg-12">
                    <hr>
                    <h2 class="intro-text text-center">Important - 
                        <strong>Disclaimer</strong>
                    </h2>
                    <hr>
                    <img class="img-responsive img-border img-left" src="img/a9.jpg" alt="">
                    <hr class="visible-xs">
                    <p>This site is for educational purposes only so anything you do with the info gained by this site is upto you and we are not responsible for it.</p>
                    <p>Each and every content in this site is for your and your use only, so do not share it with others or pulbilish it elsewhere! If you do so you might have to pay hefty charges as it is all copyrighted and doing so is illeagal.</p>
                    <a href='https://www.symptoma.com/en/info/covid-19'>What is the Corona Virus</a> <script type='text/javascript' src='https://www.freevisitorcounters.com/auth.php?id=050a125f92adb507beb9451e4b4988f829677dc8'></script>
<script type="text/javascript" src="https://www.freevisitorcounters.com/en/home/counter/707730/t/0"></script>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container -->
 

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <p>Copyright &copy; Engineers Eden june 2020</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Script to Activate the Carousel -->
    <script>
    $('.carousel').carousel({
        interval: 5000 //changes the speed
    })
    </script>

</body>

</html>

<?php

} else {
    header("location:siteclosed/index.php");
}
?>