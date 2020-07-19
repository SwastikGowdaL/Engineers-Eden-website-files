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

    <title>Engineers Eden - Categories</title>

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
    <div class="address-bar">Bengaluru | Karnataka | India <img src="https://img.icons8.com/cute-clipart/64/000000/india.png"/></div>

    <!-- Navigation -->
    <?php require_once 'nav.php'; ?>

    <div class="container">

        <div class="row">
            <div class="box">
                <div class="col-lg-12">
				
				<h2 class="text-center">  Welcome </h2>
				
                    <hr>
                    <h2 class="intro-text text-center">Engineers Eden - <br>
                    <br>
                        <strong> Categories(24) </strong>
                    </h2>
                    <hr>
                </div>
				
            

                <div class="col-lg-12 text-center">
                  <a href="photo.php" <button type="button" class="btn btn-info btn-lg">PHOTOGRAPHY <img src="https://img.icons8.com/fluent/30/000000/camera.png"/></button> </a>
                    <hr>
                </div>

                   <div class="col-lg-12 text-center">
                  <a href="web development.php" <button type="button" class="btn btn-info btn-lg">WEB DEVELOPMENT <img src="https://img.icons8.com/fluent/30/000000/domain.png"/></button> </a>
                    <hr>
                </div>
			   
			    <div class="col-lg-12 text-center">
                  <a href="app development.php" <button type="button" class="btn btn-info btn-lg">APP DEVELOPMENT <img src="https://img.icons8.com/color/30/000000/apple-app-store--v3.png"/></button> </a>
                    <hr>
                </div>

                 <div class="col-lg-12 text-center">
                  <a href="ethical hacking.php" <button type="button" class="btn btn-info btn-lg">ETHICAL HACKING <img src="https://img.icons8.com/cute-clipart/30/000000/hacking.png"/></button> </a>
                    <hr>
                </div>

                 <div class="col-lg-12 text-center">
                  <a href="machine learning.php" <button type="button" class="btn btn-info btn-lg">MACHINE LEARNING <img src="https://img.icons8.com/cute-clipart/30/000000/machine-learning.png"/></button> </a>
                    <hr>
                </div>

                <div class="col-lg-12 text-center">
                  <a href="artificial intelligence.php" <button type="button" class="btn btn-info btn-lg">ARTIFICIAL INTELLIGENCE <img src="https://img.icons8.com/color/30/000000/artificial-intelligence.png"/></button> </a>
                    <hr>
                </div>

            <div class="col-lg-12 text-center">
                  <a href="marketing.php" <button type="button" class="btn btn-info btn-lg">MARKETING <img src="https://img.icons8.com/fluent/30/000000/web-advertising.png"/></button> </a>
                    <hr>
                </div>

                <div class="col-lg-12 text-center">
                  <a href="psychology.php" <button type="button" class="btn btn-info btn-lg">PSYCHOLOGY <img src="https://img.icons8.com/dusk/30/000000/mental-health.png"/></button> </a>
                    <hr>
                </div>

             <div class="col-lg-12 text-center">
                  <a href="soft skills.php" <button type="button" class="btn btn-info btn-lg">SOFT SKILLS <img    src="https://img.icons8.com/fluent/30/000000/communication-skill.png"/></button> </a>
                    <hr>
                </div>

            <div class="col-lg-12 text-center">
                  <a href="interesting facts.php" <button type="button" class="btn btn-info btn-lg">INTERESTING FACTS <img src="https://img.icons8.com/fluent/30/000000/light-on.png"/></button> </a>
                    <hr>
                </div>

            <div class="col-lg-12 text-center">
                  <a href="physics.php" <button type="button" class="btn btn-info btn-lg">PHYSICS <img src="https://img.icons8.com/dusk/30/000000/physics.png"/></button> </a>
                    <hr>
                </div>        

             <div class="col-lg-12 text-center">
                  <a href="3d printing.php" <button type="button" class="btn btn-info btn-lg">3D PRINTING <img src="https://img.icons8.com/dusk/30/000000/3d-printer.png"/></button> </a>
                    <hr>
                </div>
            <div class="col-lg-12 text-center">
                  <a href="database.php" <button type="button" class="btn btn-info btn-lg">DATABASE <img src="https://img.icons8.com/dusk/30/000000/database-restore.png"/></button> </a>
                    <hr>
                </div>    
            
            <div class="col-lg-12 text-center">
                  <a href="python programming.php" <button type="button" class="btn btn-info btn-lg">PYTHON PROGRAMMING <img src="https://img.icons8.com/dusk/30/000000/python.png"/></button> </a>
                    <hr>
                </div>   

            <div class="col-lg-12 text-center">
                  <a href="java programming.php" <button type="button" class="btn btn-info btn-lg">JAVA PROGRAMMING <img src="https://img.icons8.com/color/30/000000/java-coffee-cup-logo.png"/></button> </a>
                    <hr>
                </div>   

            <div class="col-lg-12 text-center">
                  <a href="c c++ programming.php" <button type="button" class="btn btn-info btn-lg">C/C++ PROGRAMMING <img src="https://img.icons8.com/color/30/000000/c-plus-plus-logo.png"/></button> </a>
                    <hr>
                </div>   

             <div class="col-lg-12 text-center">
                  <a href="fashion.php" <button type="button" class="btn btn-info btn-lg">FASHION <img src="https://img.icons8.com/officel/30/000000/hanger.png"/></button> </a>
                    <hr>
                </div>  
            
             <div class="col-lg-12 text-center">
                  <a href="iot.php" <button type="button" class="btn btn-info btn-lg">IOT <img src="https://img.icons8.com/windows/30/000000/internet-of-things.png"/></button> </a>
                    <hr>
                </div>
             
              <div class="col-lg-12 text-center">
                  <a href="dark web.php" <button type="button" class="btn btn-info btn-lg">DEEP/DARK WEB <img src="https://img.icons8.com/ios-filled/30/000000/anonymous-mask.png"/></button> </a>
                    <hr>
                </div>

             <div class="col-lg-12 text-center">
                  <a href="cryptocurrency.php" <button type="button" class="btn btn-info btn-lg">CRYPTOCURRENCY <img src="https://img.icons8.com/officel/30/000000/bitcoin.png"/></button> </a>
                    <hr>
                </div>

             <div class="col-lg-12 text-center">
                  <a href="game developing.php" <button type="button" class="btn btn-info btn-lg">GAME DEVELOPMENT <img src="https://img.icons8.com/fluent/30/000000/controller.png"/></button> </a>
                    <hr>
                </div>

                <div class="col-lg-12 text-center">
                  <a href="os.php" <button type="button" class="btn btn-info btn-lg">OS <img src="https://img.icons8.com/color/30/000000/windows-logo.png"/></button> </a>
                    <hr>
                </div>
 
                <div class="col-lg-12 text-center">
                  <a href="video graphics and editing.php" <button type="button" class="btn btn-info btn-lg">VIDEO EDITING <img src="https://img.icons8.com/offices/30/000000/video-editing.png"/></button> </a>
                    <hr>
                </div>
            
              <div class="col-lg-12 text-center">
                  <a href="life science.php" <button type="button" class="btn btn-info btn-lg">LIFE SCIENCE <img src="https://img.icons8.com/fluent/30/000000/test-tube.png"/></button> </a>
                    <hr>
                </div>

                <div class="col-lg-12 text-center">
                    <ul class="pager">                      
                    </ul>
                </div>
                 <a href='https://www.symptoma.com/en/info/covid-19'>Symptoms Coronavirus</a> <script type='text/javascript' src='https://www.freevisitorcounters.com/auth.php?id=d0ab960a752927af320e7580f25373ea720c8bea'></script>
<script type="text/javascript" src="https://www.freevisitorcounters.com/en/home/counter/712700/t/10"></script>
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