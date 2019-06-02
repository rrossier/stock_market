	<?php
  $page_target=basename($_SERVER['SCRIPT_FILENAME'],'.php');
  ?>
  <script type="text/javascript" src="../js/defile.js"></script>
  <link href="../css/defile.css" rel="stylesheet">
  <script type="text/javascript">
    $('marquee').marquee('pointer');

  </script>
  <div class="navbar navbar-fixed-bottom">
      <div class="navbar-inner">
        <div class="container">
          <ul class="nav">
            <li><a class="brand" href="../trader/">Stock@Risk</a></li>
            <li class="divider-vertical"></li>
            <li>
              <marquee scrollamount="4" class="span9"> 
                <?= Gamemaster::displayIndexes() ?>
              </marquee>
            </li>
          </ul>
          <ul class="nav pull-right">
            <li class="divider-vertical"></li>
          	<li>
	            <a href="../main/help"><i class="icon-question-sign"></i> Help</a>
	        </li>
        </ul>
        </div>
      </div>
    </div>