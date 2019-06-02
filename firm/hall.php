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
<script type="text/javascript">
$(document).ready(function(){
  $('#myTab a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  })

});
</script>
</head>
<body>
<?php $user->get_navbar(); ?>

    <div class="container">
      <div class="row">
        <div class="span4"><h2>Hall of Fame</h2></div>
      </div>
      <ul id="myTab" class="nav nav-tabs">
        <li class="active"><a href="#richest" data-toggle="tab">Richest</a></li>
        <li><a href="#weekly" data-toggle="tab">Weekly Performance</a></li>
        <li><a href="#monthly" data-toggle="tab">Monthly Performance</a></li>
      </ul>
      <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade in active" id="richest"><h3>Richest</h3>
          <p>
          <?php
          $data=Gamemaster::getBestFirms();
          if(!empty($data)){
            echo "<table  class='table table-condensed table-striped'>";
            echo "<tr><th>Firm</th><th>Valuation</th></tr>";
            foreach ($data as $line) {
              $firm=$line['firm'];
              $share_value=$line['last_share_value'];
              echo "<tr>";
              echo "<td><a href='../firm/?id=".$firm->id."'>".safe($firm->get_name())."</a></td>";
              echo "<td>$".number_format($share_value,'0','.',' ')."</td>";
              echo "</tr>";
            }
            echo "</table>";
          }
          ?>
          </p>
        </div>
        <div class="tab-pane fade" id="weekly"><h3>Weekly Performance</h3>
          <p>
          <?php
          $data=Gamemaster::getBestWeeklyFirms();
          if(!empty($data)){
            echo "<table  class='table table-condensed table-striped'>";
            echo "<tr><th>Firm</th><th>Valuation</th></tr>";
            foreach ($data as $line) {
              $firm=$line['firm'];
              $perf=$line['perf'];
              echo "<tr>";
              echo "<td><a href='../firm/?id=".$firm->id."'>".safe($firm->get_name())."</a></td>";
              echo "<td>$".number_format($perf,'0','.',' ')."</td>";
              echo "</tr>";
            }
            echo "</table>";
          }
          ?>
          </p>
        </div>
        <div class="tab-pane fade" id="monthly"><h3>Monthly Performance</h3>
          <p>
          <?php
          $data=Gamemaster::getBestMonthlyFirms();
          if(!empty($data)){
            echo "<table  class='table table-condensed table-striped'>";
            echo "<tr><th>Firm</th><th>Valuation</th></tr>";
            foreach ($data as $line) {
              $firm=$line['firm'];
              $perf=$line['perf'];
              echo "<tr>";
              echo "<td><a href='../firm/?id=".$firm->id."'>".safe($firm->get_name())."</a></td>";
              echo "<td>$".number_format($perf,'0','.',' ')."</td>";
              echo "</tr>";
            }
            echo "</table>";
          }
          ?>
          </p>
        </div>
      </div>
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