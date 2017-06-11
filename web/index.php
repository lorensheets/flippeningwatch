<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;


// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));



// Database connection

$dbopts = parse_url(getenv('DATABASE_URL'));
$app->register(new Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider('pdo'),
               array(
                'pdo.server' => array(
                   'driver'   => 'pgsql',
                   'user' => $dbopts["user"],
                   'password' => $dbopts["pass"],
                   'host' => $dbopts["host"],
                   'port' => $dbopts["port"],
                   'dbname' => ltrim($dbopts["path"],'/')
                   )
               )
);

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');

  $st = $app['pdo']->prepare('SELECT * FROM crypto');
  $st->execute();

  $api = array();

  $row = $st->fetch(PDO::FETCH_ASSOC);
  array_push($api, $row['btc']);
  array_push($api, $row['eth']);
  array_push($api, $row['pct']);
  array_push($api, $row['btc_vol']);
  array_push($api, $row['eth_vol']);
  array_push($api, $row['pct_vol']);
  array_push($api, $row['eth_price']);
  array_push($api, $row['btc_rwd']);
  array_push($api, $row['eth_rwd']);
  array_push($api, $row['pct_rwd']);
  array_push($api, $row['btc_tx']);
  array_push($api, $row['btc_nodes']);
  array_push($api, $row['mkt_cap']);
  array_push($api, $row['btc_price']);



  return $app['twig']->render('index.twig', array(
    'api' => $api
  ));

});

/*
$app->get('/db/', function() use($app) {

  $st = $app['pdo']->prepare('SELECT * FROM crypto');
  $st->execute();

  $api = array();

  $row = $st->fetch(PDO::FETCH_ASSOC);
  array_push($api, $row['btc']);
  array_push($api, $row['eth']);
  array_push($api, $row['pct']);
  array_push($api, $row['btc_vol']);
  array_push($api, $row['eth_vol']);
  array_push($api, $row['pct_vol']);
  array_push($api, $row['eth_price']);
  array_push($api, $row['btc_rwd']);
  array_push($api, $row['eth_rwd']);
  array_push($api, $row['pct_rwd']);
  array_push($api, $row['btc_tx']);
  array_push($api, $row['btc_nodes']);
  array_push($api, $row['mkt_cap']);



  return $app['twig']->render('database.twig', array(
    'api' => $api
  ));


});
*/

$app->get('/script1', function() use($app) {
  return $app['twig']->render('script1.html');
});

$app->get('/api/{mkt_cap}/{btc}/{eth}/{pct}/{btc_vol}/{eth_vol}/{pct_vol}/{btc_price}/{eth_price}/{btc_rwd}/{eth_rwd}/{pct_rwd}/{btc_tx}/{btc_nodes}', function($mkt_cap,$btc,$eth,$pct,$btc_vol,$eth_vol,$pct_vol,$btc_price,$eth_price,$btc_rwd,$eth_rwd,$pct_rwd,$btc_tx,$btc_nodes) use($app) {
  $v = $app->escape($mkt_cap);
  $v1 = $app->escape($btc);
  $v2 = $app->escape($eth);
  $v3 = $app->escape($pct);
  $v4 = $app->escape($btc_vol);
  $v5 = $app->escape($eth_vol);
  $v6 = $app->escape($pct_vol);
  $v7 = $app->escape($eth_price);
  $v8 = $app->escape($btc_rwd);
  $v9 = $app->escape($eth_rwd);
  $v10 = $app->escape($pct_rwd);
  $v11 = $app->escape($btc_tx);
  $v12 = $app->escape($btc_nodes);
  $v13 = $app->escape($btc_price);

  $truncate = $app['pdo']->prepare('TRUNCATE crypto');
  $truncate->execute();

  $insert = $app['pdo']->prepare("INSERT INTO crypto (mkt_cap,btc,eth,pct,btc_vol,eth_vol,pct_vol,eth_price,btc_rwd,eth_rwd,pct_rwd,btc_tx,btc_nodes,btc_price) VALUES ( '$v','$v1','$v2','$v3','$v4','$v5','$v6','$v7','$v8','$v9','$v10','$v11','$v12','$v13' )");
  $insert->execute();

  return $v.'<br>'
  .$v1.'<br>'
  .$v2.'<br>'
  .$v3.'<br>'
  .$v4.'<br>'
  .$v5.'<br>'
  .$v6.'<br>'
  .$v7.'<br>'
  .$v8.'<br>'
  .$v9.'<br>'
  .$v10.'<br>'
  .$v11.'<br>'
  .$v12.'<br>'
  .$v13;
});



$app->run();
