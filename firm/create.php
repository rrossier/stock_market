<?php
include('../main/header.php');
@session_start();
if(isset($_SESSION['user'])){
$tp=new User($_SESSION['user']['id']);
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
      <script src="<?=racine();?>http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
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
function calcul_fees(){
    nb_shares=$("#nb_shares").val();
    if(nb_shares>=100){
        amount = nb_shares * <?= PRICE_IPO; ?>;
        fees=Math.round(100*amount*<?=Gamemaster::getPctFeesFirmCreation();?>)/100;
        $("#fees").val(fees);
        $("#total_amount").val(amount+fees);
        $("#nb_shares").parent("div").parent('div').removeClass().addClass('control-group');
        $("#error_shares").text('');
    }
    else{
        $("#nb_shares").parent("div").parent('div').addClass('error');
        $("#error_shares").text('Minimum of 100 shares');
        $("#fees").val();
        $("#total_amount").val();
    }   
}
function isAvailable(){
    name = $("#name").val();
    $.ajax({
      url:'../ajax/isNameAvailable.php',
      type: "POST",
      data:"name="+name,
      dataType: "json",
      beforeSend:function(data){
        $("#name").parent("div").parent('div').addClass('info');
        $("#name").parent().nextAll().find('span').text('');
        $("#status_name").text('Checking availability...');
    },
      success: function(bool) {
        if(bool=='true'){
            $("#name").parent("div").parent('div').removeClass().addClass('control-group success');
            $("#status_name").text('Name available.');
        }
        else{
            $("#name").parent("div").parent('div').removeClass().addClass('control-group error');
            $("#status_name").text('Name already taken.');
        }
      }
    });
}

$(document).on("click","button.save",function(event){
  //event.preventDefault();
  name=$("#name").val();
  slogan = $("#slogan").val();
  nb_shares = $("#nb_shares").val();
  id_user=<?=$id; ?>;
  hash = '<?=$hash;?>';
  $.ajax({
      url:'../ajax/ajaxRegisterFirm.php',
      type: "POST",
      data:"name="+name+"&slogan="+slogan+"&nb_shares="+nb_shares+"&id_user="+id_user+"&hash="+hash,
      dataType: "json",
      beforeSend:function(data){
          $('#results_transaction').html('');
          $('#saving').show();
    },
      success: function(tab) {
        $('#saving').hide();
        $("#results_transaction").show();
        $("#results_transaction").html(tab.result);
        setTimeout(function(){$("#results_transaction").fadeOut("slow")},3000);
      }
    });
});

</script>
</head>
<body>
<?php $user->get_navbar(); ?>

     <div class="container">
      <div id="saving" style="display:none;" class="alert">
        Registering new firm...
      </div>
      <div id="results_transaction">
      </div>
        <h3>Create a new Firm</h3><hr/>
        <div class="form-horizontal">
          <div class="control-group">
              <label class="control-label" for="name">Name</label>
              <div class="controls">
                  <input class="input-large" type="text" id="name" name="name" placeholder="name" onchange="isAvailable()">
                  <span class="help-inline" id="status_name"></span>
              </div>
          </div>
          <div class="control-group">
              <label class="control-label" for="slogan">Slogan</label>
              <div class="controls">
                  <input class="input-large" type="text" id="slogan" name="slogan" placeholder="slogan">
              </div>
          </div>
          <div class="control-group">
              <label class="control-label" for="share_value">Share Value</label>
              <div class="controls">
                  <div class="input-prepend">
                      <span class="add-on">$</span>
                      <input class="input-medium" type="text" id="share_value" value="<?= PRICE_IPO; ?>" disabled>
                  </div>
              </div>
          </div>
          <div class="control-group">
              <label class="control-label" for="nb_shares">Number of Shares</label>
              <div class="controls">
                  <input class="input-large" type="text" id="nb_shares" name="nb_shares" placeholder="200" onchange="calcul_fees()">
                  <span class="help-inline" id="error_shares"></span>
              </div>
          </div>
          <div class="control-group">
              <label class="control-label" for="fees">Registering Fees</label>
              <div class="controls">
                  <div class="input-prepend">
                      <span class="add-on">$</span>
                      <input class="input-medium" type="text" id="fees" value="" disabled>
                      
                  </div>
              </div>
          </div>
          <div class="control-group">
              <label class="control-label" for="total_amount">Total Amount</label>
              <div class="controls">
                  <div class="input-prepend">
                      <span class="add-on">$</span>
                      <input class="input-medium" type="text" id="total_amount" value="0" disabled>
                  </div>
              </div>
          </div>
          <div class="form-actions">
              <button class="btn btn-primary save">Register</button>
              <button class="btn">Cancel</button>
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