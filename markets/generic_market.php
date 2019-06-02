<?php

$page_target=htmlspecialchars(basename($_SERVER['SCRIPT_FILENAME'],'.php'));
switch ($page_target) {
  case 'dowjones':
    $market="Dow Jones";
    $location="new-york";
    break;
  case 'ftse':
    $market="FTSE";
    $location="london";
    break;
  case 'hsi':
    $market="HSI";
    $location="hong-kong";
    break;
  case 'nasdaq':
    $market="NASDAQ";
    $location="new-york";
    break;
  
  default:
    header('Location:../');
    break;
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
    <link href="../css/style.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .market-open, .up{
        color: green;
      }
      .market-closed, .down{
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
     <script src="../js/tablesorter.js"></script>
<script type='text/javascript'>//<![CDATA[ 
$(window).load(function(){
  request(true);
  var refreshing = setInterval(request, 60 * 1000);
  $("#stop").click(function(){
    clearInterval(refreshing);
    $("#datetime").append(" - Refresh stopped");
    $("#stop").hide();
  });
  function request(bool_override) {
    var bool_override = bool_override || false;
    var market='<?=$page_target;?>';
      $(function() {
          $.ajax({
            url: '../ajax/get.php',
            type: "GET",
            data: 'market='+market+'&override='+bool_override,
            dataType: "json",
            beforeSend:function(data){
              $('#loading').show();
          },
            success: function(result) {
              if(result[0].update){
                var tab="<thead><tr><th>Stock</th><th>Ask</th><th>Bid</th><th>Change</th><th>Order</th><th>Volume</th></tr></thead><tbody>";
                var timestamp=new Date();
                $("#datetime").html(timestamp);
                $.each(result[1], function(i, quote) {
                  bid=(quote.bid_realtime==0 || quote.bid_realtime=='N/A') ? (( (quote.bid=='N/A' || quote.bid==0) ? quote.last_trade : quote.bid)) : quote.bid_realtime;
                  ask=(quote.ask_realtime==0 || quote.ask_realtime=='N/A') ? (( (quote.ask=='N/A' || quote.ask==0) ? quote.last_trade : quote.ask)) : quote.ask_realtime;
                  classe=(quote.change_realtime>=0) ? 'up' : 'down';
                  currency=quote.currency;
                  tab+='<tr class="'+classe+'"><td><a href="../trader/stock.php?stock='+quote.symbol+'">'+quote.name+'</a></td><td id="ask'+quote.symbol+'">'+quote.currency+' '+ask+'</td><td id="bid'+quote.symbol+'">'+quote.currency+' '+bid+'</td><td>'+quote.change_realtime+'</td>';
                  //tab+='<td><button id="action.php?mode=buy&uuid_stock='+quote.symbol+'&id_user=<?=$id;?>&hash=<?=$hash;?>" class="order">Buy</button></td>';
                  //tab+='<td><button id="action.php?mode=sell&uuid_stock='+quote.symbol+'&id_user=<?=$id;?>&hash=<?=$hash;?>" class="order">Sell</button></td>';
                  
                  tab+='<td><a href="#myModal" role="button" class="btn" data-toggle="modal" data-id="'+quote.symbol+'" data-name="'+quote.name+'" data-price="'+ask+'">Place Order</a></td>';
                  tab+='<td>'+quote.volume+'</td></tr>';
                });
                  tab+="</tbody>";
                  $('#results').html(tab);
              }
              $('#loading').hide();
              $("#results").tablesorter();   
            }
          });
      });
  }
});

$(document).on("click","button.order",function(event){
  position=$("#position").val();
  ticker = $("#stockId").val();
  id_user=<?=$id; ?>;
  hash = '<?=$hash;?>';
  type = $("#type_order").val();
  price = $("#priceorder").val();
  //action.php?mode=buy&uuid_stock='+quote.symbol+'&id_user=<?=$id;?>&hash=<?=$hash;?>
  //target=$(this).attr('id');
  target ='../ajax/action_stock.php?mode='+position+'&uuid_stock='+ticker+'&id_user='+id_user+'&hash='+hash+'&type_order='+type+'&price='+price;
  qty=$("#qty").attr('value');
  $.ajax({
      url: target,
      type: "GET",
      data:"qty="+qty,
      dataType: "json",
      beforeSend:function(data){
          $('#results_transaction').html('');
          $('#saving').fadeIn("slow");
          $('#myModal').modal('hide');
    },
      success: function(tab) {
        if(tab.bool){
          $("#valuation").html("\$ "+tab.valuation);
          $('#portfolio').html('<h3>Portfolio</h3>'+tab.portfolio);
        }
        $('#saving').hide();
        $("#results_transaction").show();
        $("#results_transaction").html(tab.result);
        setTimeout(function(){$("#results_transaction").fadeOut("slow")},3000);
            //countdown();
      }
    });
});

$(document).on("click", ".btn", function () {
     var stockId = $(this).data('id');
     var stockname = $(this).data('name');
     var priceorder = $(this).data('price');
     $(".modal-body #stockId").val( stockId );
     $(".modal-body #stockname").val( stockname );
     $(".modal-body #priceorder").val( priceorder );
     $("#qty").val(0);
     $("#amount").val("");
     $("#fees").val("");
     $("#total").val("");
});
function jq( myid ) {
 
    return "#" + myid.replace( /(:|\.|\[|\])/g, "\\$1" );
 
}
function calculamount(){
  ticker=$("#stockId").val();
  qty=$("#qty").val();
  position=$("#position").val();
  if(position=="sell"){
    price= $(jq("bid"+ticker)).text().substr(2);
  }
  else{
    price= $(jq("ask"+ticker)).text().substr(2);
  }
  amount= Math.round((Number(price) * Number(qty))*100)/100;
  id_user=<?=$id; ?>;
  hash = '<?=$hash;?>';
  $("#amount").val(amount);
  $.ajax({
      url: '../ajax/getfees.php',
      type: "POST",
      data:'amount='+amount+'&id_user='+id_user+'&hash='+hash,
      dataType: "json",
      beforeSend:function(data){
          $("#amount").after('<img src="../img/ajax-loader.gif" alt="loader" id="ajax-loader_s" />');
      },
      success: function(tab) {
        $('#ajax-loader_s').remove();
        if(tab.bool){
          $("#fees").val(tab.fees);
            if(position=="sell"){
              $("#total").val(Math.round((amount - Number(tab.fees))*100)/100);
            }
            else{
              $("#total").val(Math.round((amount + Number(tab.fees))*100)/100);
            }
        }
        else{
          alert("Error: "+tab.error);
          $('#myModal').modal('hide');
          return(false);
        }
      }
    });
}
</script>


</head>
<body>
<?php $user->get_navbar(); ?>

    <div class="container">
      <div class="row">
        <div class="span4"><h2><?=$market;?></h2></div>
        <div class="span4"><?php $market=new Market();$market->display_time($location);?></div>
      </div>
<div id="inputSymbol"> 
  <div id="datetime">
  </div>
  <button ID="stop">Stop Refresh</button> 
</div>
<?php include('../trader/modal.php'); ?>
<div id="countdown">
</div>
<div id="loading" style="display:none;" class="alert alert-info">
  Loading...
</div>
<div id="saving" style="display:none;" class="alert">
  Saving Transaction...
</div>
<div id="results_transaction">
</div>
<table id="results" class="table table-condensed table-striped tablesorter"> <!---->
</table>
<div id="bottom">
  <div id="portfolio" class="well"><h3>Portfolio</h3>
    <?php
    echo $user->get_portfolio($hash);
    ?>
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
    <script src="../js/bootstrap-typeahead.js"></script>
</body>


<?php include('../main/footer.php'); ?>