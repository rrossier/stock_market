<?php
include('header.php');
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

   <style type="text/css"></style>
  </head>

  <body data-spy="scroll" data-target=".subnav" data-offset="50">

<?php $user->get_navbar(); ?>

    <div class="container">

<!-- Masthead
================================================== -->
<header class="jumbotron subhead" id="overview">
  <h1>Stock@Risk</h1>
  <p class="lead">All your trades are belong to us...</p>
  <div class="subnav">
    <ul class="nav nav-pills">
      <li class="active"><a href="#overview">Overview</a></li>
      <li><a href="#trader">Trader</a></li>
      <li><a href="#broker">Broker</a></li>
      <li><a href="#markets">Markets</a></li>
      <li><a href="#orders">Orders</a></li>
      <li><a href="#firms">Firms</a></li>
    </ul>
  </div>
</header>


<!-- Overview
================================================== -->
<section id="overview">
  <div class="page-header">
    <h1>Overview <small>Your first time here? Take a look before even thinking of trading...</small></h1>
  </div>
  <!-- Headings & Paragraph Copy -->
  <div class="row">
    <div class="span8">
      <h3>Principle</h3>
      <p><strong>Stockatrisk</strong> is a trading game based on stock markets.<br/>
        Stocks from New-York (S&P500, Dow Jones, Nasdaq), Paris (SRD), London (FTSE) and Forex are used with a 15-minute delay.
        Unlike other trading simulations, you can choose your role: <code>Trader</code> or <code>Broker</code>.
        <ul>
          <li>
            A trader will place orders (buy or sell) with the aim of maximizing his own portfolio's value.
            For each order successfully executed, a broker is required to transmit the order. <strong>A trader has to choose a broker before executing his orders.</strong><br/>
          </li>
          <li>
            A broker on the other hand will not place orders but instead will decide the fees his clients have to pay for each transaction.
          </li>
        </ul>
      </p>
      <p>Traders can organize themselves in <code>Firms</code> while Brokers have to adapt to markets fluctuations in order to stay competitive.</p>
    </div>
    <div class="span4">
      <h3>Hall of Fame</h3>
      <p>
      Each day, the traders are ranked based on their daily performance. A monthly performance is also displayed if the trader has not joined a firm during the last month.
    </p>
    <p>
      Brokers are also ranked based on their performance and the number of orders processed.
    </p>
    <p>
      Firms are only ranked once a week when internal composition hasn't changed (no new member or departure during a week).
    </p>
    </div>
  </div>
</section>


<!-- Trader
================================================== -->
<section id="trader">
  <div class="page-header">
    <h1>Trader <small>Greed is good...</small></h1>
  </div>

  <!-- Headings & Paragraph Copy -->
  <div class="row">
    <div class="span4">
      <h2>What to do?</h2>
      <p>You can buy and sell all stocks and currencies during open hours. For each transaction, your broker will take a fee. You have to choose a broker (see <code><a href="brokers.php">Brokers</a></code>) to execute orders. By default, Standard Broker is used.</p>
      <p>You can use different kind of <code><a href="#orders">Orders</a></code> depending on your strategies.</p>
    </div>
    <div class="span4">
      <h2>Goal</h2>
      <p>You begin with an initial capital of <span class="label label-important">$20 000</span>. To increase your portfolio's valuation, you buy and sell stocks at at higher price than you bought them.</p>
      <p>Only on the SRD (Paris), short selling is allowed (meaning you can sell before buying stocks). Careful, losses on short sells are limitless, a stock can go as high as it wants!</p>
    </div>
    <div class="span4">
      <h2>Lone wolf vs Team Spirit</h2>
      <p>Whether you like trading alone in your multi-screen room while drinking coffee and writing Black-Scholes formula all night or you enjoy the company of your peers and you know the value of being well accompanied, you can decide your own fate.</p>
      <p>You can create a <code><a href="#firm">Firm</a></code> with a minimal amount of <span class="label label-important">$15 000</span> and start building the next Goldman Sachs or Lehman Brothers...</p>
    </div>
  </div>
</section>



<!-- Broker
================================================== -->
<section id="broker">
  <div class="page-header">
    <h1>Broker <small>a quick look at the other side</small></h1>
  </div>
  <div class="row">
    <div class="span4">
      <h2>What's my job?</h2>
      <p>You begin with an initial capital of <span class="label label-important">$10 000</span>. You will earn money when traders will use your services to execute orders.</p>
    </div><!--/span-->
    <div class="span8">
      <h2>How can I get rich?</h2>
      <p>Easy... Choose your fees that traders will pay when using your services! Too high, you will earn a lot of money but only if someone is lazy enough to not check your concurrents...<br/>Too low, maybe you won't earn not much but you certainly will be the most famous.</p>
      <p>Did I tell you that as a broker, you also have to pay? Oh sorry I forgot...<br/>Well, each day you will pay at least $200 of taxes. Or 15% or your daily receipt if you're successful enough to earn so much.</p>
      <p>Actually it wasn't so easy, wasn't it?</p>
    </div><!--/span-->
  </div><!--/row-->
</section>



<!-- Tables
================================================== -->
<section id="markets">
  <div class="page-header">
    <h1>Markets <small>What happens in the trading floor stays in the trading floor</small></h1>
  </div>
  <div class="row">
    <div class="span4">
      <h2>New-York</h2>
      <p>The following indexes are available for trading:
        <ul>
          <li><a href="http://en.wikipedia.org/wiki/NASDAQ-100" target="_blank">Nasdaq 100</a></li>
          <li><a href="http://en.wikipedia.org/wiki/Dow_Jones_Industrial_Average" target="_blank">Dow Jones Industrial Average</a></li>
          <li><a href="http://en.wikipedia.org/wiki/S%26P_500" target="_blank">S&P 500</a></li>
        </ul>
      </p>
      <p>
        These indexes are all traded in New-York, open from 9:30 am to 4:00 pm, Monday to Friday (GMT -5:00 EST).
      </p>
    </div>
    <div class="span4">
      <h2>Paris, London and Hong-Kong</h2>
      <p>The following indexes are available for trading:
        <ul>
          <li><a href="http://en.wikipedia.org/wiki/FTSE_100_Index" target="_blank">FTSE 100</a></li>
          <li><a href="https://indices.nyx.com/en/products/indices/FR0003999481-XPAR?page=1" target="_blank">SBF 120</a></li>
          <li><a href="http://www.hsi.com.hk/HSI-Net/" target="_blank">Hang Seng Index</a></li>
        </ul>
      </p>
      <p>
        SBF 120 in Paris is open from 09:30 am to 05:35 pm (GMT +1:00).
      </p>
      <p>
        FTSE in London is open from 08:00 am to 04:30 pm (GMT).
      </p>
      <p>
        HSI in Hong-Kong is open from 09:30 am to 04:00 pm (GMT +6:00).
      </p>
    </div>
    <div class="span4">
      <h2>Forex</h2>
      <p>The following indexes are available for trading:
        <ul>
          <li><a href="http://en.wikipedia.org/wiki/Foreign_exchange_market" target="_blank">FOREX</a></li>
        </ul>
      </p>
      <p>
        Forex is often used as a treatment for insomnia since it's open from 20:15 GTM on Sunday until 22:00 GMT Friday.
      </p>
      <p>
        You don't want to pay fees to your broker for nothing, so be careful to place a considerable larger order than you would on a classic stock transaction!
      </p>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      <div class="alert alert-info">
      All stocks available in <code>Markets</code> are 15-minute delayed. 
      </div>
    </div>
  </div>
</section>



<!-- Forms
================================================== -->
<section id="orders">
  <div class="page-header">
    <h1>Orders</h1>
  </div>

  <h2>Three types of orders</h2>
  <p>Stockatrisk provides a simple order system up to three styles of common financial transactions.</p>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Cost</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>Market order (default)</th>
        <td>A market order is the simplest of the order types. This order type does not allow any control over the price received.<br/>
          The order is filled at the best price available at the relevant time.<br/>
          The price paid or received may be slightly different from the last quote before the order was entered.</td>
        <td>Usual fee depending on your broker</td>
      </tr>
      <tr>
        <th>Limit order</th>
        <td>A limit order is an order to buy a stock at no more than a specific price, or to sell a stock at no less than a specific price.<br/>
          A <em>buy limit</em> order can only be executed at the limit price or lower.<br/>
          A <em>sell limit</em> order is analogous; it can only be executed at the limit price or higher.<br/>
          For instance, if you want to buy stock ABC, which is trading at $12, you can set a limit order for $10.
          This guarantees that you will pay no more than $10 to buy this stock.
          Once the stock reaches $10 or less, you will automatically buy a predetermined amount of shares.<br/>
          On the other hand, if you own stock ABC and it is trading at $12, you could place a limit order to sell it at $15.
          This guarantees that the stock will be sold at $15 or more.
        </td>
        <td>Special fee depending on your broker and the number of days before its execution</td>
      </tr>
      <tr>
        <th>Stop order</th>
        <td>A stop order, also referred to as a stop-loss order, is an order to buy or sell a stock once the price of the stock reaches a specified price, known as the stop price.<br/>
          When the stop price is reached, a stop order becomes a market order. A buy stop order is entered at a stop price above the current market price.<br/>
          A sell stop order is entered at a stop price below the current market price.<br/>
          For instance, if you own stock ABC, which currently trades at $20, and you place a stop order to sell it at $15, your order will only be filled once stock ABC drops below $15.
          Also known as a "stop-loss order", this allows you to limit your losses.<br/>
          However, this type of order can also be used to guarantee profits. For example, assume that you bought stock XYZ at $10 per share and now the stock is trading at $20 per share.
          Placing a stop order at $15 will guarantee profits of approximately $5 per share, depending on how quickly the market order can be filled.</td>
        <td>Special fee depending on your broker and the number of days before its execution</td>
      </tr>
    </tbody>
  </table>
  <div class="row">
    <div class="span12">
      <div class="alert alert-info">
      An order not executed after 10 days is automatically deleted (no charge). 
      </div>
    </div>
  </div>
</section>



<!-- Buttons
================================================== -->
<section id="firms">
  <div class="page-header">
    <h1>Firms</h1>
  </div>
  <div class="row">
    <div class="span12">
      <p>
      In order to create a Firm, you need to have at least <span class="label label-important">$10 000</span> in cash. This money will then be converted in 100 shares of $100 each.
      <strong>At the end of each day, valuation of the firm will be calculated based on the firm's portfolio.</strong>
      </p>
      <p>
        Different positions are available in a firm: <span class="label">Junior Associate</span>, <span class="label label-info">Senior Associate</span>, <span class="label label-warning">Junior Partner</span>, <span class="label label-important">Senior Partner</span> and <span class="label label-inverse">Managing Partner</span>.
        Note that only Managing Partner and Senior Partners need to buy shares of the firm. Other positions don't need to buy to be hired.
      </p>
      <p>
      You don't need to be part of an actual firm to buy and sell shares of a firm. Hence you can detain shares of different firms and not work in any.
      </p>
      <div class="alert alert-success">
      <strong>At the end of each month, dividends are payed to shareholders according to the firm policy.</strong>
      </div>
      <div class="alert alert-info">
      <strong>Associates and Partners are paid at the end of each week according to their contract.</strong>
      </div>
    </div>
  </div>
<div class="row">
  <div class="span12">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Position</th>
        <th>Description</th>
        <th>Abilities</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>Junior Associate (default)</th>
        <td>When recruited in a firm, you automatically start as a junior associate. You have a credit line granted by the senior partners and you can't overtake that amount.<br/>
          <strong>Each week you get paid by the firm.</strong> Your salary is determined at your entry and can be reevaluated once a week.
        </td>
        <td>Can only trade whithin the amount defined by senior partners.</td>
      </tr>
      <tr>
        <th>Senior Associate</th>
        <td>The logical step after junior associate. Your credit line is defined by the senior partners and is bigger than junior associates. You can't overrule junior associates' decisions.<br/>
        <strong>Each week you get paid by the firm.</strong> Your salary is determined at your entry and can be reevaluated once a week.</td>
        <td>Can only trade whithin the amount defined by senior partners.</td>
      </tr>
      <tr>
        <th>Junior Partner</th>
        <td>Once you gained enough credit, your peers can promote you as Junior Partner.<br/>
          You can now overrule junior and senior associates and moreover <strong>you have access to 100% of the firm funds.</strong><br/>
          In order to trade a certain amount you need at least 50% of yes-votes while only 30% of no-votes can reject your proposition.<br/>
        </td>
        <td>Access to 100% of the firm funds, can overrule associates decisions. Need 50% of votes to place large orders.</td>
      </tr>
      <tr>
        <th>Senior Partner</th>
        <td>You have access to 100% of the firm funds, you can overrule anyone decisions (except the managing partner) with no vote required.<br/>
          On the other hand <strong>you have to buy your partnership.</strong> With a minimal amount of <span class="label label-important">$15 000</span> you get the corresponding number of shares.</td>
        <td>Access to 100% of the firm funds, can overrule associates and junior partners decisions. <strong>Can fire or hire new associates.</strong></td>
      </tr>
      <tr>
        <th>Managing Partner</th>
        <td>You are the head of the firm. You can do whatever pleases you...</td>
        <td>Access to 100% of the funds, hire, fire, overrule everyone...</td>
      </tr>
    </tbody>
  </table>
</div>
</div>
</section>

     <!-- Footer
      ================================================== -->
      <footer class="footer">
        <p class="pull-right"><a href="#">Back to top</a></p>
        <p>Designed and built with all the greed in the world <a href="http://twitter.com/__Romain__" target="_blank">@__Romain__</a>.</p>
        <p>Based on <a href="http://twitter.github.com/bootstrap/index.html">Twitter Bootstrap</a>.</p>
        <p>Stocks from <a href="http://finance.yahoo.com/">Yahoo Finance</a>.</p>
      </footer>

    </div><!-- /container -->



    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../js/jquery-1.7.2.min.js"></script>
    <script src="../js/prettify.js"></script>
    <script src="../js/bootstrap-transition.js"></script>
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
    <script src="../js/application.js"></script>

  

</body></html>