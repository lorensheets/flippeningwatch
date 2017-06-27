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
  array_push($api, $row['mkt_cap']);
  array_push($api, $row['btc_price']);

  $uv = $app['pdo']->prepare('SELECT * FROM currencies');
  $uv->execute();

  while ($row = $uv->fetch(PDO::FETCH_ASSOC)) {
    array_push($api, $row['currency']);
    array_push($api, $row['price']);
    array_push($api, $row['cap']);
  }

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

$app->get('/api/{mkt_cap}/{btc}/{eth}/{pct}/{btc_vol}/{eth_vol}/{pct_vol}/{btc_price}/{eth_price}/{btc_rwd}/{eth_rwd}/{pct_rwd}/{btc_tx}',
 function($mkt_cap,$btc,$eth,$pct,$btc_vol,$eth_vol,$pct_vol,$btc_price,$eth_price,$btc_rwd,$eth_rwd,$pct_rwd,$btc_tx) use($app) {
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
  $v12 = $app->escape($btc_price);

  /* truncate tables */
  $truncate = $app['pdo']->prepare('TRUNCATE crypto');
  $truncate->execute();

  /* insert data */
  $insert = $app['pdo']->prepare("INSERT INTO crypto (mkt_cap,btc,eth,pct,btc_vol,eth_vol,pct_vol,eth_price,btc_rwd,eth_rwd,pct_rwd,btc_tx,btc_price) VALUES ( '$v','$v1','$v2','$v3','$v4','$v5','$v6','$v7','$v8','$v9','$v10','$v11','$v12' )");
  $insert->execute();

  /* table 1 api */
  $ax = curl_init();
  curl_setopt($ax, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ax, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ax, CURLOPT_URL, 'https://api.coinmarketcap.com/v1/global/');
  $result1 = curl_exec($ax);
  curl_close($ax);

  $obj1 = json_decode($result1);
  $mktcap = $obj1->total_market_cap_usd;



  /* table 2 api */
  /* json api for currencies from coinmarketcap */
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, 'https://api.coinmarketcap.com/v1/ticker/');
  $result = curl_exec($ch);
  curl_close($ch);

  $obj = json_decode($result);

  $btc1 = (int)$obj[0]->market_cap_usd;
  $eth1 = (int)$obj[1]->market_cap_usd;
  $pct1 = ($eth/$btc)*100;

  $vol = array();

  foreach ($obj[0] as $key => $value) {
    array_push($vol, $key);
  }

  $currencies = array();
  $prices = array();
  $caps = array();

  /* truncate table */
  $truncate_currencies = $app['pdo']->prepare('TRUNCATE currencies');
  $truncate_currencies->execute();

  for ($i = 0; $i < 11; $i++) {
    $currency = $obj[$i]->name;
    $currency_price = $obj[$i]->price_usd;
    $currency_cap = $obj[$i]->market_cap_usd;
    array_push($currencies, $currency);
    array_push($prices, $currency_price);
    array_push($caps, $currency_cap);
  }

  for ($i = 0; $i < 11; $i++) {
    $table2_insert = $app['pdo']->prepare("INSERT INTO currencies (currency, price, cap) VALUES ( '$currencies[$i]','$prices[$i]','$caps[$i]' ) ");
    $table2_insert->execute();
  }

  /* print results */
  /*
  return $mktcap.'<br>'
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
  .$v12.'<br><br>'
  .$currencies[0]." ".$prices[0]." ".$caps[0]."<br>".
  $currencies[1]." ".$prices[1]." ".$caps[1]."<br>".
  $currencies[2]." ".$prices[2]." ".$caps[2]."<br>".
  $currencies[3]." ".$prices[3]." ".$caps[3]."<br>".
  $currencies[4]." ".$prices[4]." ".$caps[4]."<br>".
  $currencies[5]." ".$prices[5]." ".$caps[5]."<br>".
  $currencies[6]." ".$prices[6]." ".$caps[6]."<br>".
  $currencies[7]." ".$prices[7]." ".$caps[7]."<br>".
  $currencies[8]." ".$prices[8]." ".$caps[8]."<br>".
  $currencies[9]." ".$prices[9]." ".$caps[9]."<br>".
  $currencies[10]." ".$prices[10]." ".$caps[10];
  */

  return $vol[7];

});


$app->get('/currencies', function() use($app) {

  /* json api for currencies from coinmarketcap */
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, 'https://api.coinmarketcap.com/v1/ticker/');
  $result = curl_exec($ch);
  curl_close($ch);

  $obj = json_decode($result);

  $currencies = array();
  $prices = array();
  $caps = array();

  /* truncate table */
  $truncate_currencies = $app['pdo']->prepare('TRUNCATE currencies');
  $truncate_currencies->execute();

  for ($i = 0; $i < 11; $i++) {
    $currency = $obj[$i]->name;
    $currency_price = $obj[$i]->price_usd;
    $currency_cap = $obj[$i]->market_cap_usd;
    array_push($currencies, $currency);
    array_push($prices, $currency_price);
    array_push($caps, $currency_cap);
  }

  for ($i = 0; $i < 11; $i++) {
    $table2_insert = $app['pdo']->prepare("INSERT INTO currencies (currency, price, cap) VALUES ( '$currencies[$i]','$prices[$i]','$caps[$i]' ) ");
    $table2_insert->execute();
  }

  /* insert data */


  return   $currencies[0]." ".$prices[0]." ".$caps[0]."<br>".
  $currencies[1]." ".$prices[1]." ".$caps[1]."<br>".
  $currencies[2]." ".$prices[2]." ".$caps[2]."<br>".
  $currencies[3]." ".$prices[3]." ".$caps[3]."<br>".
  $currencies[4]." ".$prices[4]." ".$caps[4]."<br>".
  $currencies[5]." ".$prices[5]." ".$caps[5]."<br>".
  $currencies[6]." ".$prices[6]." ".$caps[6]."<br>".
  $currencies[7]." ".$prices[7]." ".$caps[7]."<br>".
  $currencies[8]." ".$prices[8]." ".$caps[8]."<br>".
  $currencies[9]." ".$prices[9]." ".$caps[9]."<br>".
  $currencies[10]." ".$prices[10]." ".$caps[10];

});

$app->run();
