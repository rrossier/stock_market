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
    <script src="../js/highstock.js"></script>
    <script src="../js/exporting.js"></script>
    <script type='text/javascript'>//<![CDATA[ 
$(window).load(function () {
  $(function() {
    str="<?= safe($_GET['stock']);?>";
    $.getJSON('../ajax/ajax_historique.php?id='+str, function(data) {

      // split the data set into ohlc and volume
      var ohlc = [],
        volume = [],
        dataLength = data.length;
        
      for (i = 0; i < dataLength; i++) {
        ohlc.push([
          data[i][0], // the date
          data[i][1], // open
          data[i][2], // high
          data[i][3], // low
          data[i][4] // close
        ]);
        
        volume.push([
          data[i][0], // the date
          data[i][5] // the volume
        ])
      }

      // set the allowed units for data grouping
      var groupingUnits = [
        [
          'day',
          [1]
        ],
        [
          'week',
          [1]
        ],
        [
          'month',
          [1, 2, 3, 4, 6]
        ]
      ];

      // create the chart
      $('#chart').highcharts('StockChart', {
          
          rangeSelector: {
              selected: 1
          },

          title: {
              text: str+' Historical'
          },

          yAxis: [{
              title: {
                  text: 'OHLC'
              },
              height: 200,
              lineWidth: 2
          }, {
              title: {
                  text: 'Volume'
              },
              top: 300,
              height: 100,
              offset: 0,
              lineWidth: 2
          }],
          
          series: [{
              type: 'candlestick',
              name: str,
              data: ohlc,
              dataGrouping: {
            units: groupingUnits
              }
          }, {
              type: 'column',
              name: 'Volume',
              data: volume,
              yAxis: 1,
              dataGrouping: {
            units: groupingUnits
              }
          }]
      });
    });
  });
});

</script>
</head>
<body>
<?php $user->get_navbar(); ?>

    <div class="container">
<?php
if( (isset($_POST['stock']) && !empty($_POST['stock'])) || (isset($_GET['stock']) && !empty($_GET['stock'])) ){
  $stock=new Stock();
  $uuid=(isset($_POST['stock'])) ? htmlspecialchars($_POST['stock']) : htmlspecialchars($_GET['stock']);
  $tab=array_unique(array_merge($dowjones_tickers,$nasdaq_tickers,$sp500_tickers,$ftse_tickers,$srd_tickers,$forex_tickers,$hsi_tickers));
  $stock->__set('uuid',$uuid);
  if(!in_array($uuid, $tab)){
    echo "<div class='alert alert-error'>No stock registered with this ticker.</div>";
  }
  else{
    $stock->get_last_values();
    $stock_names=array_unique(array_merge($dowjones_names,$nasdaq_names,$sp500_names,$ftse_tickers,$srd_tickers,$forex_names,$hsi_names));
    $stock_name=$stock->get_name();
    //$news=display_news($uuid);
    echo "<div class='well'>";
    echo "<h2>".$stock_name."</h2>";
    echo "<h3>Bid: ".$stock->get_currency()." ".$stock->__get('bid')."</h3>";
    echo "<h3>Ask: ".$stock->get_currency()." ".$stock->__get('ask')."</h3>";
    echo "<h3>Volume:  ".number_format($stock->__get('volume'),0,'.',' ')."</h3>";
    echo "</div>";
    echo '<div id="chart" class="well" style="height: 500px; min-width: 400px"></div>';
    echo '<div id="saving" style="display:none;" class="alert">Saving Transaction...</div><div id="results_transaction"></div>';
    echo '<ul class="thumbnails">';
    //echo "<li class='span4'><div class='thumbnail'><img src='http://chart.finance.yahoo.com/z?s=".$uuid."&t=6m&q=l&l=on&z=s&p=m50,m200'/><h5>50-day Moving Average (green)</h5><h5>20-day Moving Average (red)</h5></div></li>";
    //echo "<li class='span4'><div class='thumbnail'><img src='http://chart.finance.yahoo.com/z?s=".$uuid."&t=6m&q=l&l=on&z=s&p=e50,e200'/><h5>50-day Exponential Moving Average (green)</h5><h5>200-day Exponential Moving Average (red)</h5></div></li>";
    //echo "<li class='span4'><div class='thumbnail'><img src='http://chart.finance.yahoo.com/z?s=".$uuid."&t=6m&q=l&l=on&z=s&p=b,v'/><h5>Bollinger Bands</h5><h5>Volume</h5></div></li>";
    echo '</ul>';
    /*
    echo "<h2>News</h2><div class='row'>";
    $news1=array_slice($news,0,5);
    $news2=array_slice($news,5,5);
    echo "<div class='span6'><dl>";
    foreach($news1 as $new){
      echo "<dt><a href='".$new['link']."'>".htmlspecialchars($new['title'])."</a></dt>";
      echo "<dd><i>".trim($new['description'])."</i></dd>";
      echo "<dd><span class='label label-info'>".htmlspecialchars($new['pubdate'])."</span></dd><br/>";
    }
    echo "</dl></div>";
    echo "<div class='span6'><dl>";
    foreach($news2 as $new){
      echo "<dt><a href='".$new['link']."'>".htmlspecialchars($new['title'])."</a></dt>";
      echo "<dd><i>".trim($new['description'])."</i></dd>";
      echo "<dd><span class='label label-info'>".htmlspecialchars($new['pubdate'])."</span></dd><br/>";
    }
    echo "</dl></div>";
    echo "</div>";
    */
  }
}
else{
  echo '<div class="accordion" id="accordion2">
    <div class="accordion-group">
      <div class="accordion-heading">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">Dow Jones</a>
      </div>
      <div id="collapseOne" class="accordion-body in collapse" style="height: auto; ">
        <div class="accordion-inner">';
        $tickers=Gamemaster::getStocks('dowjones');
        foreach($tickers as $line){
          echo "<a href='?stock=".$line['ticker']."'>".$line['name']."</a>&nbsp;&nbsp;&nbsp;";
        }
        echo '</div>
      </div>
    </div>
    <div class="accordion-group">
      <div class="accordion-heading">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">Nasdaq 100</a>
      </div>
      <div id="collapseTwo" class="accordion-body collapse">
        <div class="accordion-inner">';
        $tickers=Gamemaster::getStocks('nasdaq');
        foreach($tickers as $line){
          echo "<a href='?stock=".$line['ticker']."'>".$line['name']."</a>&nbsp;&nbsp;&nbsp;";
        }
        echo '</div>
      </div>
    </div>
    <div class="accordion-group">
      <div class="accordion-heading">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">S&P 500</a>
      </div>
      <div id="collapseThree" class="accordion-body collapse">
        <div class="accordion-inner">';
        $tickers=Gamemaster::getStocks('sp500');
        foreach($tickers as $line){
          echo "<a href='?stock=".$line['ticker']."'>".$line['name']."</a>&nbsp;&nbsp;&nbsp;";
        }
        echo '</div>
      </div>
    </div>
    <div class="accordion-group">
      <div class="accordion-heading">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseFour">FTSE</a>
      </div>
      <div id="collapseFour" class="accordion-body collapse">
        <div class="accordion-inner">';
        $tickers=Gamemaster::getStocks('ftse');
        foreach($tickers as $line){
          echo "<a href='?stock=".$line['ticker']."'>".$line['name']."</a>&nbsp;&nbsp;&nbsp;";
        }
        echo '</div>
      </div>
    </div>
    <div class="accordion-group">
      <div class="accordion-heading">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseFive">SBF 120</a>
      </div>
      <div id="collapseFive" class="accordion-body collapse">
        <div class="accordion-inner">';
        $tickers=Gamemaster::getStocks('sbf120');
        foreach($tickers as $line){
          echo "<a href='?stock=".$line['ticker']."'>".$line['name']."</a>&nbsp;&nbsp;&nbsp;";
        }
        echo '</div>
      </div>
    </div>
    <div class="accordion-group">
      <div class="accordion-heading">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseSix">HSI</a>
      </div>
      <div id="collapseSix" class="accordion-body collapse">
        <div class="accordion-inner">';
        $tickers=Gamemaster::getStocks('hsi');
        foreach($tickers as $line){
          echo "<a href='?stock=".$line['ticker']."'>".$line['name']."</a>&nbsp;&nbsp;&nbsp;";
        }
        echo '</div>
      </div>
    </div>
  <div class="accordion-group">
      <div class="accordion-heading">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseSeven">Forex</a>
      </div>
      <div id="collapseSeven" class="accordion-body collapse">
        <div class="accordion-inner">';
        $tickers=Gamemaster::getStocks('forex');
        foreach($tickers as $line){
          echo "<a href='?stock=".$line['ticker']."'>".$line['name']."</a>&nbsp;&nbsp;&nbsp;";
        }
        echo '</div>
      </div>
    </div>
  </div>';
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