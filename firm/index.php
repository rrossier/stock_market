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
    <script type='text/javascript'>//<![CDATA[ 
    $(document).on("click", "button#apply", function () {
        var id_firm = $(this).data('id');
        var qty = $("#nb_shares").val();
        id_user=<?=$id; ?>;
        hash = '<?=$hash;?>';
        $.ajax({
            url: '../ajax/join_firm.php',
            type: "POST",
            data:"id_user="+id_user+"&hash="+hash+"&id_firm="+id_firm+"&qty="+qty,
            dataType: "json",
            beforeSend:function(data){
                $("#results").show();
                $('#results').html('<div class="alert alert-info">Waiting</div>');
            },
            success: function(tab) {
                $("#results").show();
                $("#results").html(tab.result);
            }
          });
           
      });
</script>
</head>
<body>
<?php $user->get_navbar(); ?>

     <div class="container">
        <div class="row-fluid">
          <div class="span12" id="results"></div>
        </div>
        <?php
        if( (isset($_POST['id']) && !empty($_POST['id'])) || (isset($_GET['id']) && !empty($_GET['id'])) ){
            $id_firm=(isset($_POST['id']) && !empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : htmlspecialchars($_GET['id']);
            $firm = new Firm($id_firm);
            //echo $firm->get_portfolio();
            //var_dump($firm);
            $founder=$firm->getFounder();
            $manager=$firm->getManager();
            echo "<h2>".htmlspecialchars($firm->get_name())."</h2>";
            echo "<h4>Founder: ".htmlspecialchars($founder->get_name())." <small>".date('d-M-Y H:i',$firm->datetime)."</small></h4>";
            echo "<h4>Manager: ".htmlspecialchars($manager->get_name())."</h4>";
            echo "<h4>Valuation: $".number_format($firm->get_valuation(),'2','.',' ')."</h4>";
            echo "<h4>Shareholders: ".count($firm->id_private_shareholders)."</h4>";
            if($user->has_firm($id_firm)){
                // Private info

                // links to manage
            }
            else{
                // Public info

                // apply
                echo '<div class="well"><h4>Apply</h4>';
                echo '<div class="form-inline">';
                echo '<label class="control-label" for="nb_shares">Number of Shares: </label>';
                echo '<input class="input-mini" type="text" name="nb_shares" id="nb_shares" placeholder="50">';
                echo ' <button class="btn btn-primary" type="button" id="apply" data-id="'.$id_firm.'">Apply</button>';
                echo '</div></div>';
            }
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