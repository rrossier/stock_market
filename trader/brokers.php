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
</head>
<body>
<?php $user->get_navbar(); ?>

    <div class="container">

<div id="bottom">
	<div id="brokers" class="well"><h2>Brokers</h2>
    <form action="" method="post">
		<?php
    if(isset($_POST['broker'])){
      $id=$_POST['broker'];
      $user->__set('id_broker',$id);
      if($user->precise_save('id_broker',$id)){
        $message="<div class='alert alert-success'>Broker registered with success!</div>";
      }
      else{
        $message="<div class='alert alert-error'>Error . No Broker registered</div>";
      }
    }
		$brokers=get_list_brokers();
    if(isset($message)){echo $message;}
    echo '<table class="table table-condensed">
      <tr><th>Name</th><th>Minimum Charge</th><th>Classic Fee</th><th>Additional Fee (per day)</th><th>Number of transactions</th><th>Since</th><th>Select</th></tr>';
    foreach ($brokers as $key => $line) {
      echo "<tr><td>".$line->__get('name')."</td><td> \$".$line->__get('frais_min');
      echo "</td><td>".($line->__get('frais')*100)."%</td>";
      echo "</td><td>".($line->__get('additional_fees')*100)."%</td>";
      echo "<td>".count($line->get_historique());
      echo "</td><td>".date('F jS, Y',strtotime($line->__get('date_inscription')))."</td>";
      echo "<td><input type='radio' name='broker' value='".$line->__get('id')."'";
      if($line->__get('id')==$user->__get('id_broker')){echo "checked";}
      echo "/></tr>";
    }
		?>
    </table>
    <input type="submit" value="Save"/>
    </form>
	</div>
</div>
<hr>

      <footer>
        <p>&copy; StockAtRisk 2012</p>
      </footer>

    </div>
    <?php $user->get_navbar_bottom(); ?>
     <script src="../js/jquery-1.7.2.min.js"></script>
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
    <script src="../js/bootstrap-typeahead.js"></script>
</body>


<?php include('../main/footer.php'); ?>