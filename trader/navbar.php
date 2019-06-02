	<?php
  $array=explode('/',dirname($_SERVER['SCRIPT_FILENAME']));
  $page_target=basename($_SERVER['SCRIPT_FILENAME'],'.php');
  $dir=end($array);
  ?>
  <div class="navbar navbar-fixed-top navbar-inverse">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="../trader/">Stock@Risk</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="divider"></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Hall of Fame <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li <?php if($page_target=='hall' && $dir=='trader'){echo 'class="active"';} ?>><a href="../trader/hall">Traders</a></li>
                  <li <?php if($page_target=='hall' && $dir=='firm'){echo 'class="active"';} ?>><a href="../firm/hall">Firms</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Brokers <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li <?php if($page_target=='brokers'){echo 'class="active"';} ?>><a href="../trader/brokers">Select</a></li>
                  <li <?php if($page_target=='loan'){echo 'class="active"';} ?>><a href="../broker/loan">Get a Loan</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Markets <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li <?php if($page_target=='dowjones'){echo 'class="active"';} ?>><a href="../markets/dowjones">Dow Jones</a></li>
                  <li <?php if($page_target=='nasdaq'){echo 'class="active"';} ?>><a href="../markets/nasdaq">NASDAQ</a></li>
                  <li <?php if($page_target=='sp500'){echo 'class="active"';} ?>><a href="../markets/sp500">S&P 500</a></li>
                  <li <?php if($page_target=='sbf120'){echo 'class="active"';} ?>><a href="../markets/sbf120">SBF120 Paris</a></li>
                  <li <?php if($page_target=='ftse'){echo 'class="active"';} ?>><a href="../markets/ftse">FTSE London</a></li>
                  <li <?php if($page_target=='hsi'){echo 'class="active"';} ?>><a href="../markets/hsi">HSI Hong-Kong</a></li>
                  <li <?php if($page_target=='forex'){echo 'class="active"';} ?>><a href="../markets/forex">FOREX</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Firms <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li <?php if($page_target=='list'){echo 'class="active"';} ?>><a href="../firm/list">List</a></li>
                  <li <?php if($page_target=='create'){echo 'class="active"';} ?>><a href="../firm/create">Create</a></li>
                  <li <?php if($page_target=='manage'){echo 'class="active"';} ?>><a href="../firm/manage">Manage</a></li>
                  <li <?php if($page_target=='quotes'){echo 'class="active"';} ?>><a href="../firm/quotes">VC</a></li>
                </ul>
              </li>
            </ul>
            <form class="navbar-search pull-left" action="">
              <input type="text" name="q" id="q" class="search-query span2" placeholder="Search" autocomplete="off"/>
            </form>
            <ul class="nav pull-right">
              <!--
              <li><a>Quantity:</a></li>
              <li><input type="text" class="navbar-search input-mini span1" placeholder="Quantity" value="10" name="qty" id="qty"/></li>
              -->
            	<li class="divider-vertical"></li>
              <li class="dropdown">
                <a href="#" id="valuation" class="dropdown-toggle" data-toggle="dropdown">Portfolio <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a>Cash: $ <? echo number_format($this->get_cash(),0,'.',' ') ; ?></a></li>
                  <li><a>Stocks: $ <? echo number_format($this->portfolio_valuation(),0,'.',' ') ; ?></a></li>
                  <li><a>P.E.: $ <? echo number_format($this->portfolio_firms_valuation(),0,'.',' ') ; ?></a></li>
                  <li class="divider"></li>
                  <li <?php if($page_target=='portfolio'){echo 'class="active"';} ?>><a href="../trader/portfolio">Total: $ <? echo number_format($this->get_valuation(),0,'.',' ') ; ?></a></li>
                </ul>
              </li>
              <li class="divider-vertical"></li>
            	<li class="dropdown">
		            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
		            	<?php

                  echo htmlspecialchars($this->name);
                  $nb_notifs=$this->getNbNotifs();
                  if($nb_notifs!==FALSE){
                    echo ' <span class="badge badge-info" id="nb_notifs">'.$nb_notifs.'</span>';
                  }
                  ?>
		              <b class="caret"></b>
		            </a>
		            <ul class="dropdown-menu">
		              <li <?php if($page_target=='profile'){echo 'class="active"';} ?>><a href="../trader/profile"><i class="icon-user"></i> Profile</a></li>
                  <li <?php if($page_target=='portfolio'){echo 'class="active"';} ?>><a href="../trader/portfolio"><i class="icon-briefcase"></i> Portfolio</a></li>
                  <li <?php if($page_target=='plan_orders'){echo 'class="active"';} ?>><a href="../trader/plan_orders"><i class="icon-calendar"></i> Planify Orders</a></li>
		              <li class="divider"></li>
                  <li><a href="../logout"><i class="icon-remove"></i> Sign Out</a></li>
		            </ul>
		        </li>
          </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>