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
function search_firm(){
  q=$("#search_firm").val();
  $.ajax({
      url: '../ajax/search_firm.php',
      type: "POST",
      data:"q="+q,
      dataType: "json",
      beforeSend:function(data){
          $('#results').html('...');
    },
      success: function(result) {
        $("#results").html(result);
      }
    });
}
</script>
</head>
<body>
 <?php $user->get_navbar(); ?>

    <div class="container-fluid">
      <h2>List of recently created firms</h2>
      <table class="table table-striped">
        <thead><tr><th>Name</th><th>Founder</th><th>Manager</th><th>Valuation</th><th>Number of shares</th><th>Number of shareholders</th><th>Date of Registration</th></tr></thead>
        <tbody>
          <?php
            $firms=get_list_firms();
            foreach ($firms as $firm) {
              $founder=$firm->getFounder();
              $manager=$firm->getManager();
              $valuation=$firm->share_value * $firm->number_shares;
              $nb_shareholders=count($firm->id_private_shareholders);
              $date=date('d-M-Y H:i',$firm->datetime);
              echo "<tr>";
              echo "<td><a href='./?id=".$firm->id."'>".htmlspecialchars($firm->get_name())."</a></td>";
              echo "<td><a href='../trader/trader?id=".$founder->id."'>".htmlspecialchars($founder->get_name())."</a></td>";
              echo "<td><a href='../trader/trader?id=".$manager->id."'>".htmlspecialchars($manager->get_name())."</a></td>";
              echo "<td>$ ".number_format($valuation,'0','.',' ')."</td>";
              echo "<td>".number_format($firm->number_shares,'0','.',' ')."</td>";
              echo "<td>".number_format($nb_shareholders,'0','.',' ')."</td>";
              echo "<td>".$date."</td></tr>";
            }
          ?>
        </tbody>

      </table>
    <hr/>
    <input type="text" class="input-medium search-query" id="search_firm" placeholder="Search Firm" onkeyup="search_firm()"><br/><br/>
    <div id="results" class="well">

    </div>

    <hr/>
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