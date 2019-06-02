<?php
include('header.php');
@session_start();
if(isset($_SESSION['user'])){
$tp=new User($_SESSION['user']['id']);
$user=$tp->create();
$id=$user->__get('id');
$hash=$_SESSION['user']['hash'];
$name=$user->__get('name');
}
else{
	header('Location:index.php');
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
    <script type='text/javascript'>//<![CDATA[ 
$(document).ready(function(){
    $('.typeahead').typeahead()
});
</script>
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
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="./">Home</a></li>
              <li><a href="about/">About</a></li>
              <li><a href="brokers.php">Brokers</a></li>
            </ul>
            <form class="navbar-search pull-left" method="post" action="stock.php">
            	<input type="text" name="stock" class="search-query span2" placeholder="Search Stock" data-provide="typeahead" data-items="4" data-source='["ATVI","ADBE","AKAM","ALXN","ALTR","AMZN","AMGN","APOL","AAPL","AMAT","ADSK","ADP","AVGO","BIDU","BBBY","BIIB","BMC","BRCM","CHRW","CA","CELG","CERN","CHKP","CSCO","CTXS","CTSH","CMCSA","COST","DELL","XRAY","DTV","DLTR","EBAY","EA","EXPE","EXPD","ESRX","FFIV","FAST","FISV","FLEX","FOSL","GRMN","GILD","GOOG","GMCR","HSIC","INFY","INTC","INTU","ISRG","KLAC","KFT","LRCX","LINTA","LIFE","LLTC","MRVL","MAT","MXIM","MCHP","MU","MSFT","MNST","MYL","NTAP","NFLX","NWSA","NUAN","NVDA","ORLY","ORCL","PCAR","PAYX","PRGO","PCLN","QCOM","GOLD","RIMM","ROST","SNDK","STX","SHLD","SIAL","SIRI","SPLS","SBUX","SRCL","SYMC","TXN","VRSN","VRTX","VIAB","VMED","VOD","WCRX","WFM","WYNN","XLNX","YHOO","AA","AXP","BA","BAC","CAT","CSCO","CVX","DD","DIS","GE","HD","HPQ","IBM","INTC","JNJ","JPM","KFT","KO","MCD","MMM","MRK","MSFT","PFE","PG","T","TRV","UTX","VZ","WMT","XOM"]'/>
            </form>
            <ul class="nav pull-right">
            	<li><a href="portfolio.php" id="cash"><?="\$ ".$user->get_cash();?></a></li>
            	<li class="divider-vertical"></li>
            	<li class="dropdown">
		            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
		            	<?=$name;?>
		              <b class="caret"></b>
		            </a>
		            <ul class="dropdown-menu">
		              <li><a href="profile/">Profile</a></li>
		              <li><a href="portfolio.php">Portfolio</a></li>
		              <li class="divider"></li>
		              <li><a href="#">Sign Out</a></li>
		            </ul>
		        </li>
          </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

		<hr>

      <footer>
        <p>&copy; StockAtRisk 2012</p>
      </footer>

    </div>
     <script src="js/jquery-1.7.2.min.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>
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
</body>


<?php include('footer.php'); ?>

