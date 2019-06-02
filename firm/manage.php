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

    
    <link href="../css/bootstrap.css" rel="stylesheet">
    <!-- Le styles
    <link href="../css/docs.css" rel="stylesheet">
    -->
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
    <script src="../js/bootstrap-collapse.js"></script>
    <script type='text/javascript'>//<![CDATA[ 
$(document).ready(function(){
    $('.typeahead').typeahead();
    $("[data-toggle=tooltip]").tooltip();
});

$(document).on("click", "button.maj", function () {
  var id = $(this).data('id');
  var use = $(this).data('function');
  var id_firm = $(this).data('firm');
  id_user=<?=$id; ?>;
  hash = '<?=$hash;?>';
  if(use=='manager'){
    $.ajax({
      url: '../ajax/promote.php',
      type: "POST",
      data:"id_user="+id_user+"&hash="+hash+"&id="+id+"&id_firm="+id_firm,
      dataType: "json",
      beforeSend:function(data){
        $("#results").show();
        $('#results').html('<div class="alert alert-info">Waiting</div>');
      },
      success: function(tab) {
        $("#results").show();
        $("#results").html(tab.result);
        setTimeout(function(){$("#results").fadeOut("slow")},3000);
      }
    });
  }
  else{
    $.ajax({
      url: '../ajax/maj_firms.php',
      type: "POST",
      data:"id_user="+id_user+"&hash="+hash+"&id="+id+"&use="+use,
      dataType: "json",
      beforeSend:function(data){
        $("#results").show();
        $('#results').html('<div class="alert alert-info">Waiting</div>');
      },
      success: function(tab) {
        $("#results").show();
        $("#results").html(tab.result);
        setTimeout(function(){$("#results").fadeOut("slow")},3000);
      }
    });
  }
     
});
</script>
</head>
<body>
 <?php $user->get_navbar(); ?>

    <div class="container-fluid">
      <h2>Manage your firms</h2>
      <?php
      if($user->has_any_firm()){
        $firms=$user->get_list_firms();
        echo "<form action='' method='POST'><select name='id_firm'>";
        foreach($firms as $line){
          if($user->has_power_from_firm($line['id_firm'])){
            //var_dump($firm);
            echo "<option value='".$line['id_firm']."'>".htmlspecialchars($line['name'])."</option>";
          }
        }
        echo "</select><br/><button type='submit' class='btn btn-inverse'>Manage</button></form>";
        if(isset($_POST['id_firm'])){
          $id_firm=safe($_POST['id_firm']);
          if($user->isManager($id_firm)){
            $firm=new Firm($id_firm);
            //var_dump($firm);
            ?>
            <hr/>
            <div class="row-fluid">
              <div class="span12" id="results"></div>
            </div>
            <div class="row-fluid">
              <div class="span8 well">
                <div class="row-fluid">
                  <div class="span12">
                    <div class="form-horizontal">
                      <div class="control-group">
                          <label class="control-label" >Name</label>
                          <div class="controls">
                              <span class="label label-info"><?= htmlspecialchars($firm->get_name());?></span>
                          </div>
                      </div>
                      <div class="control-group">
                          <label class="control-label" for="slogan">Slogan</label>
                          <div class="controls">
                              <input class="input-large" type="text" id="slogan" name="slogan" placeholder="slogan">
                          </div>
                      </div>
                      <div class="control-group">
                          <label class="control-label">Share Value</label>
                          <div class="controls">
                            <span class="badge badge-important">$ <?= number_format($firm->share_value,'2','.',' ');?></span>
                          </div>
                      </div>
                      <div class="control-group">
                          <label class="control-label">Number of Shares</label>
                          <div class="controls">
                            <span class="badge badge-info"><?= number_format($firm->number_shares,'0','.',' ');?></span>
                          </div>
                      </div>
                      <div class="control-group">
                          <label class="control-label">Number of Shareholders</label>
                          <div class="controls">
                            <span class="badge badge-info"><?= number_format(count($firm->id_private_shareholders),'0','.',' ');?></span>
                          </div>
                      </div>
                      <div class="form-actions">
                          <button class="btn btn-primary save">Save changes</button>
                          <button class="btn">Cancel</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="span4 well">
                <h4>Shareholders</h4>
                <ul>
                <?php
                foreach ($firm->id_private_shareholders as $line){
                  $trader=new Trader($line['id']);
                  echo "<li>";
                  echo htmlspecialchars($trader->get_name());
                  echo " <span class='badge badge-inverse'>";
                  echo $firm->get_quotepart($line['id']);
                  echo "%</span>";
                  if($id===$line['id']){
                    echo ' <span class="label label-info">You</span>';
                  }
                  if($firm->isFounder($line['id'])){
                    echo ' <span class="label label-success">Founder</span>';
                  }
                  if($firm->isManager($line['id'])){
                    echo ' <span class="label label-warning">Manager</span>';
                  }
                  else{
                    echo ' <button class="btn btn-mini btn-danger maj" type="button" data-firm="'.$firm->id.'" data-id="'.$line['id'].'" data-function="manager">Set Manager</button>';
                  }
                  if($firm->isManager($id) && $id!==$line['id']){
                    echo ' <button class="btn btn-mini btn-danger" type="button">Demote</button>';
                  }
                  echo "</li>";
                }
                ?>
                </ul>
              </div>

              <div class="span4 well">
                <h4>Pending Requests</h4>
                <?php
                  $pending_requests=$firm->getPendingRequests();
                  if(!empty($pending_requests)){
                    echo "<ul id='requests'>";
                    foreach ($pending_requests as $row) {
                      $trader=new Trader($row['id_trader']);
                      $date=date('d-M-Y H:i',$row['datetime']);
                      echo "<li>".htmlspecialchars($trader->get_name())." (".$date.') ';
                      echo '<button class="btn btn-mini btn-success maj" type="button" data-id="'.$row['id'].'" data-function="accept">Accept</button> ';
                      echo '<button class="btn btn-mini btn-danger maj" type="button" data-id="'.$row['id'].'" data-function="reject">Reject</button></li>';
                    }
                    echo "</ul>";
                  }
                  else{
                    echo '<span class="label label-success">No pending requests</span>';
                  }
                ?>
              </div>
            </div>
            <div class="row-fluid">
              <div class="span12 well"></div>
            </div>
            <?php
          }
        }
      }
      else{
        echo '<div class="alert alert-error">You have invested in no firms yet</div>';
      }
      ?>
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