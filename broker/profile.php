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
if($user->get_type()!='broker'){
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
     <script type='text/javascript'>

      $(document).on("click", "button.close", function () {
        var id = $(this).data('id');
        id_user=<?=$id; ?>;
        hash = '<?=$hash;?>';
        $.ajax({
            url: '../ajax/update_notifs.php',
            type: "POST",
            data:"id_user="+id_user+"&hash="+hash+"&id="+id,
            dataType: "json",
            beforeSend:function(data){
            },
            success: function(tab) {
              if(tab){
                $("#"+id).hide();
                nb_notifs=parseInt($("#nb_notifs").text());
                if(nb_notifs==1){
                  $("#nb_notifs").text('');
                }
                else{
                  $("#nb_notifs").text(nb_notifs-1);
                }
              }
            }
          });
           
      });
     </script>
</head>
<body>
 <?php
$user->get_navbar();
 ?>

    <div class="container-fluid">
      <?php
      if($user->getNbNotifs()!==0){
        $notifs=$user->get_list_notifications();
        foreach ($notifs as $key => $notif) {
          echo $notif['notif'];
        }
      }
      ?>
      <div class="well">
    <h4>Historique</h4>
    <table class="table table-condensed">
      <tr><th>Trader</th><th>Ticker</th><th>Price</th><th>Quantity</th><th>Date</th></tr>
    <?php
    $tab=$user->get_historique();
    foreach($tab as $line){
      $trader=new Trader($line['id_user']);
      $trader_name=$trader->get_name();
      $id_trader=$trader->id;
      echo "<tr><td><a href='../trader/trader.php?id=".$id_trader."'>".$trader_name."</a></td><td><a href='../trader/stock.php?stock=".$line['uuid_stock']."'>".$line['uuid_stock']."</a></td><td>".$line['price']."</td><td>".$line['quantity']."</td><td>".date("d-m-y H:i:s",strtotime($line['datetime']))."</td></tr>";
    }
    ?>
    </table>
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