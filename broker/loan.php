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
    <link href="../css/bootstrap-responsive.css" rel="stylesheet">
    <link href="../css/docs.css" rel="stylesheet">
    <link href="../css/prettify.css" rel="stylesheet">
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
      <script src="../js/highstock.js"></script>
      <script src="../js/exporting.js"></script>
      <script type='text/javascript'>
      $(window).load(function () {
        $(function() {
          var seriesOptions = [],
            yAxisOptions = [],
            seriesCounter = 0,
            names = ['Overnight', '1-Week', '2-Weeks', '1-Month'],
            colors = Highcharts.getOptions().colors;

          $.each(names, function(i, name) {

            $.getJSON('../ajax/getRates.php?rate='+ name.toLowerCase(), function(data) {

              seriesOptions[i] = {
                name: name,
                data: data
              };

              // As we're loading the data asynchronously, we don't know what order it will arrive. So
              // we keep a counter and create the chart when all the data is loaded.
              seriesCounter++;

              if (seriesCounter == names.length) {
                createChart();
              }
            });
          });

          // create the chart when all data is loaded
          function createChart() {

            $('#chart').highcharts('StockChart', {
              chart: {},

              rangeSelector: {
                selected: 4
              },

              yAxis: {
                labels: {
                  formatter: function() {
                    return (this.value > 0 ? '+' : '') + this.value + '%';
                  }
                },
                plotLines: [{
                  value: 0,
                  width: 2,
                  color: 'silver'
                }]
              },
              
              plotOptions: {
                series: {
                  compare: 'percent'
                }
              },
              
              tooltip: {
                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.change}%)<br/>',
                valueDecimals: 2
              },
              
              series: seriesOptions
            });
          }
        });
      });
$(document).on("click","button.compute",function(event){
  montant=$("#montant").val();
  rate=$("#rate").val();

  $.ajax({
    url: '../ajax/computeLoan.php',
    type: "POST",
    data:"montant="+montant+"&rate="+rate,
    dataType: "json",
    beforeSend:function(data){
        $('#results').html('');
        $("#take").hide();
    },
    success: function(tab) {
      var str='<table class="table table-condensed"><tr><th>Day</th><th>Payment</th><th>Amortization</th><th>Interests</th><th>Remaining Capital</th></tr>';
      $.each(tab, function(i, line) {
        str+='<tr><td>'+(i+1)+'</td><td>$ '+line.mens+'</td><td>$ '+line.amort+'</td><td>$ '+line.interests+'</td><td>$ '+line.CRD+'</td></tr>';
      });
      str+="</table>";
      str+="<button class='btn btn-danger' id='take'>Take Loan</button>";
      $("#results").html(str);
    }
  });
});

$(document).on("click","button#take",function(event){
  montant=$("#montant").val();
  rate=$("#rate").val();
  id=<?=$id;?>;
  hash='<?=$hash;?>';

  $.ajax({
    url: '../ajax/takeLoan.php',
    type: "POST",
    data:"montant="+montant+"&rate="+rate+"&id="+id+"&hash="+hash,
    dataType: "json",
    beforeSend:function(data){
    },
    success: function(tab) {
      $('#results').html('');
      $("#results").html(tab);
    }
  });
});
</script>
</head>
<body data-spy="scroll" data-target=".subnav" data-offset="50">
 <?php
$user->get_navbar();
 ?>

    <div class="container">
      <div class="row">
        <div id="overnight" class="span12">
          <h2>Get a loan</h2>
          <p class="lead">Expand your horizons</p>
          <div id="chart" style="height: 500px; min-width: 400px"></div>
        </div>
      </div>
      <div class="row">
        <div class="span3 well">
          <div class="control-group">
            <label class="control-label" for="montant">Amount</label>
            <div class="controls">
              <input type="text" id="montant" placeholder="Amount">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="rate">Duration</label>
            <div class="controls">
              <select name="rate" id="rate">
                <option value="overnight">Overnight</option>
                <option value="week">1 Week</option>
                <option value="weeks">2 Weeks</option>
                <option value="month">1 Month</option>
              </select>
            </div>
          </div>
          <div class="control-group">
            <button type="button" class="btn compute btn-primary">Compute Payments</button>
          </div>
        </div>
        <div class="span8 well" id="results">
        </div>
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