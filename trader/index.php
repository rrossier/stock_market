<?php
include('../main/header.php');
@session_start();
if(isset($_SESSION['user'])){
$tp=new User($_SESSION['user']['id']);
$tp->__set('type',$_SESSION['user']['type']);
$user=$tp->create();
$id=$user->__get('id');
$hash=$_SESSION['user']['hash'];
$name=$user->__get('name');
}
else{
  header('Location:../');
}
if($user->get_type()!='trader'){
  header('Location:../');
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>StockAtRisk - Trading Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="../css/bootstrap.css" rel="stylesheet">
<style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
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
    <link href="../css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="../img/favicon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../img/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../img/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../img/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../img/apple-touch-icon-57-precomposed.png">
     <script src="../js/jquery-1.7.2.min.js"></script>
    <script src="../js/bootstrap-typeahead.js"></script>
<script src="../js/search.js"></script>
    <script src="../js/bootstrap-collapse.js"></script>
    <script type='text/javascript'>//<![CDATA[ 
$(document).ready(function(){
    $("[data-toggle=tooltip]").tooltip();
});
</script>
</head>
<body>
 <?php $user->get_navbar(); ?>

    <div class="container-fluid">
<div class="row-fluid">
        <div class="span3">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">Main</li>
              <li class="active"><a href="main"><i class="icon-home"></i>Dashboard</a></li>
              <li><a href="hall"><i class="icon-th-list"></i>Hall of Fame</a></li>
              <li><a href="brokers"><i class="icon-globe"></i>Brokers</a></li>
              <li class="nav-header">Markets</li>
              <li><a href="../markets/dowjones"><i class="icon-minus"></i>Dow Jones (New-York)</a></li>
              <li><a href="../markets/nasdaq"><i class="icon-minus"></i>Nasdaq (New-York)</a></li>
              <li><a href="../markets/sp500"><i class="icon-minus"></i>S&P 500 (New-York)</a></li>
              <li><a href="../markets/sbf120"><i class="icon-minus"></i>SRD (Paris)</a></li>
              <li><a href="../markets/ftse"><i class="icon-minus"></i>FTSE (London)</a></li>
              <li><a href="../markets/hsi"><i class="icon-minus"></i>HSI (Hong-Kong)</a></li>
              <li><a href="../markets/forex"><i class="icon-minus"></i>Forex</a></li>
              <li class="nav-header">User</li>
              <li><a href="profile"><i class="icon-user"></i>Profile</a></li>
              <li><a href="portfolio"><i class="icon-briefcase"></i>Portfolio</a></li>
              <li><a href="../logout"><i class="icon-remove"></i>Sign Out</a></li>
            </ul>
          </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
          <div class="row-fluid">
            <div class="span4">
              <h2><a href="#" data-placement="right" data-toggle="tooltip" title="09h30-16h00">S&P 500</a></h2>
              <p><table class="table table-condensed"><?php
              $quotes=get_best_daily_quotes('sp500');
              foreach ($quotes as $key => $quote) {
                echo "<tr><td><a href='stock?stock=".$quote['symbol']."'>".$quote['name']."</a></td>";
                if($quote['change_day']>0){
                  echo "<td class='up'>".$quote['change_day']."%</td></tr>";
                }
                else{
                  echo "<td class='down'>".$quote['change_day']."%</td></tr>";
                }
              }
              ?></table></p>
            </div><!--/span-->
            <div class="span4">
              <h2><a href="../markets/dowjones" data-placement="right" data-toggle="tooltip" title="09h30-16h00">Dow Jones 30</a></h2>
              <p><table class="table table-condensed"><?php
              $quotes=get_best_daily_quotes('dowjones');
              foreach ($quotes as $key => $quote) {
                echo "<tr><td><a href='stock?stock=".$quote['symbol']."'>".$quote['name']."</a></td>";
                if($quote['change_day']>0){
                  echo "<td class='up'>".$quote['change_day']."%</td></tr>";
                }
                else{
                  echo "<td class='down'>".$quote['change_day']."%</td></tr>";
                }
              }
              ?></table></p>
            </div><!--/span-->
            <div class="span4">
              <h2><a href="../markets/nasdaq" data-placement="right" data-toggle="tooltip" title="09h30-16h00">NASDAQ 100</a></h2>
              <p><table class="table table-condensed"><?php
              $quotes=get_best_daily_quotes('nasdaq');
              foreach ($quotes as $key => $quote) {
                echo "<tr><td><a href='stock?stock=".$quote['symbol']."'>".$quote['name']."</a></td>";
                if($quote['change_day']>0){
                  echo "<td class='up'>".$quote['change_day']."%</td></tr>";
                }
                else{
                  echo "<td class='down'>".$quote['change_day']."%</td></tr>";
                }
              }
              ?></table></p>
            </div><!--/span-->
          </div><!--/row-->
          <div class="row-fluid">
            <div class="span4">
              <h2><a href="../markets/sbf120" data-placement="right" data-toggle="tooltip" title="09h30-17h30">SBF 120</a></h2>
              <p><table class="table table-condensed"><?php
              $quotes=get_best_daily_quotes('sbf120');
              foreach ($quotes as $key => $quote) {
                echo "<tr><td><a href='stock?stock=".$quote['symbol']."'>".$quote['name']."</a></td>";
                if($quote['change_day']>0){
                  echo "<td class='up'>".$quote['change_day']."%</td></tr>";
                }
                else{
                  echo "<td class='down'>".$quote['change_day']."%</td></tr>";
                }
              }
              ?></table></p>
            </div><!--/span-->
            <div class="span4">
              <h2><a href="../markets/ftse" data-placement="right" data-toggle="tooltip" title="08h00-16h30">FTSE</a></h2>
              <p><table class="table table-condensed"><?php
              $quotes=get_best_daily_quotes('ftse');
              foreach ($quotes as $key => $quote) {
                echo "<tr><td><a href='stock?stock=".$quote['symbol']."'>".$quote['name']."</a></td>";
                if($quote['change_day']>0){
                  echo "<td class='up'>".$quote['change_day']."%</td></tr>";
                }
                else{
                  echo "<td class='down'>".$quote['change_day']."%</td></tr>";
                }
              }
              ?></table></p>
            </div><!--/span-->
            <div class="span4">
              <h2><a href="../markets/hsi" data-placement="right" data-toggle="tooltip" title="09h30-16h00">HSI</a></h2>
              <p><table class="table table-condensed"><?php
              $quotes=get_best_daily_quotes('hsi');
              foreach ($quotes as $key => $quote) {
                echo "<tr><td><a href='stock?stock=".$quote['symbol']."'>".$quote['name']."</a></td>";
                if($quote['change_day']>0){
                  echo "<td class='up'>".$quote['change_day']."%</td></tr>";
                }
                else{
                  echo "<td class='down'>".$quote['change_day']."%</td></tr>";
                }
              }
              ?></table></p>
            </div><!--/span-->
          </div><!--/row-->
        </div><!--/span-->
      </div>
    <hr>

      <footer>
        <p>&copy; StockAtRisk 2012</p>
      </footer>

    </div>
    <?php $user->get_navbar_bottom(); ?>
    <script src="../js/bootstrap-typeahead.js"></script>
    <script src="../js/bootstrap-alert.js"></script>
    <script src="../js/bootstrap-modal.js"></script>
    <script src="../js/bootstrap-dropdown.js"></script>
    <script src="../js/bootstrap-scrollspy.js"></script>
    <script src="../js/bootstrap-tab.js"></script>
    <script src="../js/bootstrap-tooltip.js"></script>
    <script src="../js/bootstrap-popover.js"></script>
    <script src="../js/bootstrap-button.js"></script>
    <script src="../js/bootstrap-carousel.js"></script>
</body>


<?php include('../main/footer.php'); ?>