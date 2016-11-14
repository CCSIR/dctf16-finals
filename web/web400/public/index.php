
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Super Secure IPS</title>

    <!-- Bootstrap core CSS -->
    <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="http://getbootstrap.com/examples/cover/cover.css" rel="stylesheet">
  <body>

    <div class="site-wrapper">

      <div class="site-wrapper-inner">

        <div class="cover-container">

          <div class="masthead clearfix">
            <div class="inner">
              <h3 class="masthead-brand">SSIPS</h3>
              <nav>
                <ul class="nav masthead-nav">
                  <li class="active"><a href="#">Home</a></li>
                  <li><a href="vuln.php?id=3">Demo</a></li>
                </ul>
              </nav>
            </div>
          </div>

          <div class="inner cover">
            <h1 class="cover-heading">Super Secure IPS</h1>
            <p class="lead">Hi there! Our security specialist developed a new IPS system with state of the art technology. He told us that his IPS is bullet proof and Web Attacks will not pass. He is so confident of this IPS that he made a SQLI vulnerability in the following link and told us that if we can get the first row from table flag, he will send us a bottle of whisky and he'll get back to support desk. Can you help us get drunk? :-) </p>
            <p class="lead">
              <a href="vuln.php?id=3" class="btn btn-lg btn-danger">Demo WAF</a>
               <a href="index.php?reset_waf" class="btn btn-lg btn-default">Reset WAF</a>

            </p>
          </div>

          <div class="mastfoot">
            <div class="inner">
              <p>Copyright &copy; Super Secure IPS 2016. All rights to those who own them.</a>.</p>
            </div>
          </div>

        </div>

      </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
  </body>
</html>


<?php

include_once('config.php');

if(isset($_GET['reset_waf'])) {
	//todo change this to default settings
	$db->query('DELETE FROM knowledge WHERE ip="'.$_SERVER['REMOTE_ADDR'].'"');
	$db->query("

INSERT INTO `knowledge` (`request`, `name`, `value`, `type`, `ip`) VALUES
('vuln.php', 'html', '{\"totallen\":774.5,\"alpha\":546.7,\"alphanums\":588.5,\"nums\":41.8,\"special\":186.1,\"ascii\":771.9,\"nonascii\":2.7}', 'RESPONSE', '".$_SERVER["REMOTE_ADDR"]."'),
('vuln.php', 'text', '{\"totallen\":774.5,\"alpha\":546.7,\"alphanums\":588.5,\"nums\":41.8,\"special\":186.1,\"ascii\":771.9,\"nonascii\":2.7}', 'RESPONSE', '".$_SERVER["REMOTE_ADDR"]."'),
('vuln.php', 'id', '{\"totallen\":1,\"alpha\":0,\"alphanums\":1,\"nums\":1,\"special\":0,\"ascii\":1,\"nonascii\":0}', 'GET', '".$_SERVER["REMOTE_ADDR"]."');


		");
}