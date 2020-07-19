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

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Engineers Eden - About us</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/business-casual.css" rel="stylesheet">

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

    <!-- Navigation -->
    <?php require_once 'nav.php'; ?>

    <div class="container">

        <div class="row">
            <div class="box">
                <div class="col-lg-12">
                    <hr>
                    <h2 class="intro-text text-center">About
                        <strong>Engineers Eden</strong>
                    </h2>
                    <hr>
                </div>
                <div class="col-md-6">
                    <script src="https://fast.wistia.com/embed/medias/48udaj99pv.jsonp" async></script><script src="https://fast.wistia.com/assets/external/E-v1.js" async></script><div class="wistia_responsive_padding" style="padding:56.25% 0 0 0;position:relative;"><div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;"><div class="wistia_embed wistia_async_48udaj99pv videoFoam=true" style="height:100%;position:relative;width:100%">&nbsp;</div></div></div>
</video>
                </div>
                <div class="col-md-6">
                    <p>Hello my AIT friends , we are a bunch of engineering students currently studying at ait who are interested in developing and designing algorithms and this time we wanted to do something for others which led to the creation of Engineers Eden.</p> 
                    <p>Our mission is to provide best user interface to the user and vission is to see knowledge being imparted to others.</p>
                    <p>SGLX - This is a Video streaming and hosting website just like youtube. This project was undertook by our team long ago which we integrated to Engineers Eden website for providing interesting videos like tutorials,seminars etc..,Note that SGLX is a beta version.</p> 
                    </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="row">
            <div class="box">
                <div class="col-lg-12">
                    <hr>
                    <h2 class="intro-text text-center">Our
                        <strong>Team</strong>
                    </h2>
                    <hr>
                </div>
                <div class="col-sm-4 text-center">
                    <img class="img-responsive" src="img/sg.jpg" alt="">
                    <h3>Swastik Gowda</h3>
                </div>
                <div class="col-sm-4 text-center">
                    <img class="img-responsive" src="img/ur.jpg" alt="">
                    <h3>Ujval DR</h3>
                </div>
                <div class="col-sm-4 text-center">
                    <img class="img-responsive" src="img/vg.jpg" alt="">
                    <h3>Vignesh G</h3>
                </div>
				 <div class="col-sm-4 text-center">
                    <img class="img-responsive" src="img/vr.jpg" alt="">
                    <h3>Vishal Ratnakar</h3>
                </div>
                <div class="col-sm-4 text-center">
                    <img class="img-responsive" src="img/rv.jpg" alt="">
                    <h3>Rahul V</h3>
                </div>
                <div class="col-sm-4 text-center">
                    <img class="img-responsive" src="img/mn.jpg" alt="">
                    <h3>Manavendra Nirgund</h3>
                </div>
                <div class="clearfix"></div>
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

</body>

</html>
<?php

} else {
    header("location:siteclosed/index.php ");
}
?>