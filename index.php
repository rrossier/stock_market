<?php
ini_set('display_errors', '1');
include('main/header.php');
session_start();
if(isset($_SESSION['user'])){
$tp=new User($_SESSION['user']['id']);
$tp->__set('type',$_SESSION['user']['type']);
$user=$tp->create();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>StockAtRisk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .up{
        color: green;
      }
      .down{
        color: red;
      }
      .marquee-up{
        padding-left:5px;
        padding-right:5px;
        color:#eee;
        background-color: green;
      }
      .marquee-down{
        padding-left:5px;
        padding-right:5px;
        color:#eee;
        background-color: red;
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="img/favicon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="img/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="img/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="img/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="img/apple-touch-icon-57-precomposed.png">
     <script src="../js/jquery-1.7.2.min.js"></script>
    <script src="../js/bootstrap-typeahead.js"></script>
<script src="../js/search.js"></script>
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="./">StockAtRisk</a>
          <?php
          if(isset($user) && !empty($user)){
            $name=$user->__get('name');
          ?>
          <div class="btn-group pull-right">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="icon-user"></i><?=$name;?>
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="profile/">Profile</a></li>
              <li class="divider"></li>
              <li><a href="logout.php">Sign Out</a></li>
            </ul>
          </div>
          <?php
          }
          ?>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="./">Home</a></li>
              <li><a href="about/">About</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <?php
      if(isset($user) && !empty($user)){
      ?>
      <div class="hero-unit">
        <h1>Hello, you!</h1>
        <p></p>
        <p><a class="btn btn-primary btn-large" href="<?= $user->get_type()?>/">Start &raquo;</a></p>
      </div>
      <?php
      }else{
      ?>
      <div class="hero-unit">
        <h1>Hello, you!</h1>
        <p></p>
        <p><a class="btn btn-primary btn-large" href="login.php">Login &raquo;</a></p>
      </div>
      <?php
      }
      ?>
      <!-- Example row of columns -->
      <div class="row">
        <div class="span4">
          <h2>Shut up and let's trade!</h2>
           <p>Sell, Buy!<br/> Barings and Lehman,<br/> Markets are going down,<br/> The world will I own</p>
          <p><a class="btn" href="main.php">Go go go!! &raquo;</a></p>
        </div>
        <div class="span4">
          <h2>Already an account ?</h2>
           <p>Hey! I didn't recognize you...<br/>I'm sorry, this way Sir!</p>
          <p><a class="btn" href="login.php">Login &raquo;</a></p>
       </div>
        <div class="span4">
          <h2>Create an account</h2>
          <p>You need to register to enter into heaven. Hell yeah! Just an email, credit card infos and security social number will do...</p>
          <p><a class="btn" href="register.php">Register &raquo;</a></p>
        </div>
      </div>

      <hr>

      <footer>
        <p>&copy; StockAtRisk Trading 2012</p>
      </footer>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-1.7.2.min.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>

  </body>
<?php include('main/footer.php'); ?>
