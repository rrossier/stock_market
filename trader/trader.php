<?php
ini_set('display_errors', '1'); 
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
      <script src="../http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
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
</head>
<body>
<?php $user->get_navbar(); ?>

     <div class="container">
        <?php
        if( (isset($_POST['id']) && !empty($_POST['id'])) || (isset($_GET['id']) && !empty($_GET['id'])) ){
        $id=(isset($_POST['id']) && !empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : htmlspecialchars($_GET['id']);
        $trader = new Trader();
        $trader->fill($id);
        //echo $firm->get_portfolio();
        echo '<div class="row-fluid">';
            echo '<div class="span12 well">';
                echo '<div class="row-fluid">';
                    echo '<div class="span2 well">';
                            $grav_url=$trader->get_gravatar();
                            echo '<img src="'.$grav_url.'" class="img-rounded">';
                    echo '</div>';
                    echo '<div class="span6 well">';
                        echo "<h2>".htmlspecialchars($trader->get_name())."</h2>";
                        echo '<dl>';
                            echo '<dt>Stock Portfolio</dt>';
                            echo '<dd>Holds '.count($trader->portfolio).' stocks in portfolio for a total value of $'.number_format($trader->portfolio_valuation(),'0','.',' ').'</dd>';
                            echo '<dt>Firms Portfolio</dt>';
                            echo '<dd>Has invested in '.count($trader->shares_firms).' firms for a total value of $'.number_format($trader->portfolio_firms_valuation(),'0','.',' ').'</dd>';
                            echo '<dt>Cash</dt>';
                            echo '<dd>$'.number_format($trader->get_cash(),'0','.',' ').'</dd>';
                        echo '</dl>';
                    echo '</div>';
                    echo '<div class="span4 well">';
                        $results=$trader->retrieve_classement();
                        if($results!==FALSE){
                            // valuations et classement
                            echo '<dl class="dl-horizontal">';
                            // day
                            $str= '<dt>Past day:</dt><dd><span class="badge badge-';
                            $str.= ($results['last_valuation']<100000) ? 'important' : 'success';
                            $str.= '">$'.number_format($results['last_valuation'],0,'.',' ').'</span>';
                            $str.= ' <span class="badge badge-info">'.$results['classement_day'].'</dd>';
                            echo $str;
                            // week
                            $str= '<dt>Past week:</dt><dd><span class="badge badge-';
                            $str.= ($results['performance_week']<0) ? 'important' : 'success';
                            $str.= '">'.number_format($results['performance_week'],2,'.',' ').'%</span>';
                            $str.= ' <span class="badge badge-info">'.$results['classement_week'].'</dd>';
                            echo $str;
                            // month
                            $str= '<dt>Past month:</dt><dd><span class="badge badge-';
                            $str.= ($results['performance_month']<0) ? 'important' : 'success';
                            $str.= '">'.number_format($results['performance_month'],2,'.',' ').'%</span>';
                            $str.= ' <span class="badge badge-info">'.$results['classement_month'].'</dd>';
                            echo $str;
                            echo "</dl>";
                        }
                    echo '</div>';
            echo "</div>";
                echo '<div class="row-fluid">';
                    echo '<div class="span12 well">';
                        echo "<p class='lead'>Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Duis mollis, est non commodo luctus.</p>";
                    echo '</div>';

            echo "</div>";
        echo "</div>";
        }
        else{
          
        }
        ?>
		<hr>

      <footer>
        <p>&copy; StockAtRisk 2012</p>
      </footer>

    </div>
    <?php $user->get_navbar_bottom(); ?>
    <script src="../js/bootstrap-alert.js"></script>
    <script src="../js/bootstrap-modal.js"></script>
    <script src="../js/bootstrap-dropdown.js"></script>
    <script src="../js/bootstrap-scrollspy.js"></script>
    <script src="../js/bootstrap-tab.js"></script>
    <script src="../js/bootstrap-tooltip.js"></script>
    <script src="../js/bootstrap-popover.js"></script>
    <script src="../js/bootstrap-button.js"></script>
    <script src="../js/bootstrap-collapse.js"></script>
    <script src="../js/bootstrap-carousel.js"></script>
</body>
<?php include('../main/footer.php'); ?>


