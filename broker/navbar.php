	<?php
  $page_target=basename($_SERVER['SCRIPT_FILENAME'],'.php');
  ?>
  <div class="navbar navbar-fixed-top navbar-inverse">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="../broker/">StockAtRisk</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="divider"></li>
              <li <?php if($page_target=='hall'){echo 'class="active"';} ?>><a href="../broker/hall">Hall of Fame</a></li>
              <li <?php if($page_target=='clients'){echo 'class="active"';} ?>><a href="../broker/clients">Clients</a></li>
            </ul>
            <form class="navbar-search pull-left" action="">
              <input type="text" name="q" id="q" class="search-query span2" placeholder="Search" autocomplete="off"/>
            </form>
            <ul class="nav pull-right">
              <li class="divider-vertical"></li>
            	<li class="dropdown">
		            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
		            	<?php

                  echo htmlspecialchars($this->name);
                  $nb_notifs=$this->getNbNotifs();
                  if($nb_notifs!==0){
                    echo ' <span class="badge badge-info" id="nb_notifs">'.$nb_notifs.'</span>';
                  }
                  ?>
		              <b class="caret"></b>
		            </a>
		            <ul class="dropdown-menu">
		              <li <?php if($page_target=='profile'){echo 'class="active"';} ?>><a href="../broker/profile"><i class="icon-user"></i> Profile</a></li>
                  <li class="divider"></li>
                  <li><a href="../main/help"><i class="icon-question-sign"></i> Help</a></li>
                  <li class="divider"></li>
                  <li><a href="../logout"><i class="icon-remove"></i> Sign Out</a></li>
		            </ul>
		        </li>
          </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>