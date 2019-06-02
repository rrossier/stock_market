<?php
include('main/header.php');
@session_start();
if(isset($_SESSION['user'])){
  $tp=new User($_SESSION['user']['id']);
  $tp->__set('type',$_SESSION['user']['type']);
}
if(isset($_POST['email']) && isset($_POST['pwd'])){
	$email=trim($_POST['email']);
	$pwd=trim($_POST['pwd']);
	$tp=new User();
	$tp->__set('email',$email);
    //$microtime=microtime(true);
	$res=$tp->authenticate($pwd);
    //$time_elapsed=microtime(true)-$microtime;
    //write_log('authenticate time: '.$time_elapsed.' ms');
	if($res!==false){
    $user=$tp->create();
    $_SESSION['user']['id']=$user->id;
    $_SESSION['user']['type']=$tp->type;
    $hash = password_hash($user->name, PASSWORD_BCRYPT);
    $_SESSION['user']['hash']=$hash;
    switch($user->get_type()){
      case 'trader':
        header('Location:trader/');
      break;

      case 'broker':
        header('Location:broker/');
      break;
    }
	}
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
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="ico/favicon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
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
        <h1>Hello, world!</h1>
        <p>This is a template for a simple marketing or informational website. It includes a large callout called the hero unit and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
        <p><a class="btn btn-primary btn-large" href="main.php">Begin &raquo;</a></p>
      </div>
      <?php
      }else{
      ?>
      <div class="hero-unit">
        <h1>Hello, world!</h1><p>
        <form class="well form-inline" method="post" action="">
  			<input type="text" class="input-large" placeholder="Email" name="email" id="email">
  			<input type="password" class="input-large" placeholder="Password" name="pwd" id="pwd">
  			<button type="submit" class="btn">Sign in</button>
		</form>
	</p>
      </div>
      <?php
      }
      ?>
      <!-- Example row of columns -->
      <div class="row">
        <div class="span4">
          <h2>Shut up and let's trade!</h2>
           <p>Sell, Buy!<br/> Barings and Lehman,<br/> My trades won't let me down,<br/> The world I will own</p>
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
