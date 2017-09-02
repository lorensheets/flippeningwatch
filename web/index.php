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


/* test */
/*
$app->get('/graphtest/', function() use($app) {

  $mk = curl_init();
  curl_setopt($mk, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($mk, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($mk, CURLOPT_URL, 'https://blockchain.info/charts/market-cap?timespan=all&format=json');
  $result_mktcap = curl_exec($mk);
  curl_close($mk);
  $data1 = json_decode($result_mktcap);
  $values = $data1->values;
  $dataset = array();
  foreach($values as $val){
    array_push($dataset, $val->y);
  }

  return $dataset;
});
*/

/* graphs */

$app->get('/charts/', function() use($app) {


  /* get crypto data from database */
  $st = $app['pdo']->prepare('SELECT * FROM crypto');
  $st->execute();

  $api = array();

  $row = $st->fetch(PDO::FETCH_ASSOC);
  array_push($api, $row['btc']);
  array_push($api, $row['eth']);
  array_push($api, $row['eth_price']);
  array_push($api, $row['btc_price']);
  array_push($api, $row['mkt_cap']);

  /* get currencies data from database */
  $ms = $app['pdo']->prepare('SELECT * FROM currencies');
  $ms->execute();

  while ($row = $ms->fetch(PDO::FETCH_ASSOC)) {
    array_push($api, $row['currency']);
    array_push($api, $row['price']);
    array_push($api, $row['cap']);
  }


  /* bitcoin historical market cap data api */
  $mk = curl_init();
  curl_setopt($mk, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($mk, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($mk, CURLOPT_URL, 'http://www.flippening.watch/btcjsondata');
  $result_mktcap = curl_exec($mk);
  curl_close($mk);
  $values = json_decode($result_mktcap);

  $dataset = array();
  $times = array();
  foreach($values as $key => $val){
    array_push($dataset, $val);
    array_push($times, $key);
  }


  /* ethereum historical market cap data api */
  $et = curl_init();
  curl_setopt($et, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($et, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($et, CURLOPT_URL, 'http://www.flippening.watch/ethjsondata');
  $mktcap = curl_exec($et);
  curl_close($et);
  $ethdata = json_decode($mktcap);

  $eth_data = array();
  $eth_dates = array();
  foreach($ethdata as $key => $val) {
    array_push($eth_data, $val);
    array_push($eth_dates, $key);
  }

  /* ripple historical market cap data api */
  $xrp = curl_init();
  curl_setopt($xrp, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($xrp, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($xrp, CURLOPT_URL, 'http://www.flippening.watch/xrpjsondata');
  $xrpmktcap = curl_exec($xrp);
  curl_close($xrp);
  $xrpdata = json_decode($xrpmktcap);

  $xrp_data = array();
  foreach($xrpdata as $key => $val) {
    array_push($xrp_data, $val);
  }

  /* bitcoin cash historical market cap data api */
  $bch = curl_init();
  curl_setopt($bch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($bch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($bch, CURLOPT_URL, 'http://www.flippening.watch/bchjsondata');
  $bchmktcap = curl_exec($bch);
  curl_close($bch);
  $bchdata = json_decode($bchmktcap);

  $bch_data = array();
  foreach($bchdata as $key => $val) {
    array_push($bch_data, $val);
  }

  /* total cryptocurrency historical market cap data api */
  $mktcap = curl_init();
  curl_setopt($mktcap, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($mktcap, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($mktcap, CURLOPT_URL, 'http://www.flippening.watch/mktcapjsondata');
  $result_totalmktcap = curl_exec($mktcap);
  curl_close($mktcap);
  $total_mktcap_values = json_decode($result_totalmktcap);

  $total_mktcap_dataset = array();

  foreach($total_mktcap_values as $key => $val){
    array_push($total_mktcap_dataset, $val);
  }


  /* render html with data */

  return $app['twig']->render('graph.twig', array(
    'dataset' => $dataset,
    'times' => $times,
    'api' => $api,
    'ethdata' => $eth_data,
    'ethdates' => $eth_dates,
    'total_mktcap_dataset' => $total_mktcap_dataset,
    'xrpdata' => $xrp_data,
    'bchdata' => $bch_data
  ));

});


/* test chart page */
$app->get('/testchart/', function() use($app) {

  /* get ticker data from database */
  $st = $app['pdo']->prepare('SELECT * FROM crypto');
  $st->execute();

  $api = array();

  $row = $st->fetch(PDO::FETCH_ASSOC);
  array_push($api, $row['btc']);
  array_push($api, $row['eth']);
  array_push($api, $row['eth_price']);
  array_push($api, $row['btc_price']);
  array_push($api, $row['mkt_cap']);

  $ms = $app['pdo']->prepare('SELECT * FROM currencies');
  $ms->execute();

  while ($row = $ms->fetch(PDO::FETCH_ASSOC)) {
    array_push($api, $row['currency']);
    array_push($api, $row['price']);
    array_push($api, $row['cap']);
  }


  /* bitcoin historical market cap data api */
  $mk = curl_init();
  curl_setopt($mk, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($mk, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($mk, CURLOPT_URL, 'http://www.flippening.watch/btcjsondata');
  $result_mktcap = curl_exec($mk);
  curl_close($mk);
  $values = json_decode($result_mktcap);

  $dataset = array();
  $times = array();
  foreach($values as $key => $val){
    array_push($dataset, $val);
    array_push($times, $key);
  }

  /* ethereum historical market cap data api */
  $et = curl_init();
  curl_setopt($et, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($et, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($et, CURLOPT_URL, 'http://www.flippening.watch/ethjsondata');
  $mktcap = curl_exec($et);
  curl_close($et);
  $ethdata = json_decode($mktcap);

  $eth_data = array();
  foreach($ethdata as $key => $val) {
    array_push($eth_data, $val);
  }

  /* ripple historical market cap data api */
  $xrp = curl_init();
  curl_setopt($xrp, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($xrp, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($xrp, CURLOPT_URL, 'http://www.flippening.watch/xrpjsondata');
  $xrpmktcap = curl_exec($xrp);
  curl_close($xrp);
  $xrpdata = json_decode($xrpmktcap);

  $xrp_data = array();
  foreach($xrpdata as $key => $val) {
    array_push($xrp_data, $val);
  }

  /* total cryptocurrency historical market cap data api */
  $mktcap = curl_init();
  curl_setopt($mktcap, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($mktcap, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($mktcap, CURLOPT_URL, 'http://www.flippening.watch/mktcapjsondata');
  $result_totalmktcap = curl_exec($mktcap);
  curl_close($mktcap);
  $total_mktcap_values = json_decode($result_totalmktcap);

  $total_mktcap_dataset = array();

  foreach($total_mktcap_values as $key => $val){
    array_push($total_mktcap_dataset, $val);
  }

  return $app['twig']->render('testchart.twig', array(
    'dataset' => $dataset,
    'times' => $times,
    'total_mktcap_dataset' => $total_mktcap_dataset,
    'ethdata' => $eth_data,
    'xrpdata' => $xrp_data,
    'api' => $api
  ));

});



/* api update */
$app->get('/apiupdate/',
 function() use($app) {

  /* truncate tables */
  $truncate = $app['pdo']->prepare('TRUNCATE crypto');
  $truncate->execute();

  /* table 1 api */

  /* json api for global stats from coinmarketcap */
  $ax = curl_init();
  curl_setopt($ax, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ax, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ax, CURLOPT_URL, 'https://api.coinmarketcap.com/v1/global/');
  $result1 = curl_exec($ax);
  curl_close($ax);
  $obj1 = json_decode($result1);

  $mktcap = $obj1->total_market_cap_usd;

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
  $pct1 = ($eth1/$btc1)*100;
  $btcvolarray = array();
  $ethvolarray = array();
  foreach ($obj[0] as $key => $value) {
    array_push($btcvolarray, $value);
  }
  foreach ($obj[1] as $key => $value) {
    array_push($ethvolarray, $value);
  }
  $btcvol = (int)$btcvolarray[6];
  $ethvol = (int)$ethvolarray[6];
  $pctvol = ($ethvol/$btcvol)*100;
  $btcprice = $obj[0]->price_usd;
  $ethprice = $obj[1]->price_usd;
  $btcrwd = $btcprice * 1800;

  /* api for ethereum block time from etherchain */
  $eh = curl_init();
  curl_setopt($eh, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($eh, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($eh, CURLOPT_URL, 'https://etherchain.org/api/miningEstimator');
  $result2 = curl_exec($eh);
  curl_close($eh);
  $obj2 = json_decode($result2);

  $ethrwd = (int)(86400 / (int)( $obj2->data[0]->blockTime ) * 5 * $ethprice);
  $pctrwd = ($ethrwd/$btcrwd)*100;


  /* api for bitcoin transactions from blockchain info */
  $fh = curl_init();
  curl_setopt($fh, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($fh, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($fh, CURLOPT_URL, 'https://blockchain.info/q/24hrtransactioncount?&cors=true');
  $result3 = curl_exec($fh);
  curl_close($fh);
  $obj3 = json_decode($result3);

  $btctx = (int)$obj3;


  /* insert data into table 1*/
  $insert = $app['pdo']->prepare("INSERT INTO crypto (mkt_cap,btc,eth,pct,btc_vol,eth_vol,pct_vol,eth_price,btc_rwd,eth_rwd,pct_rwd,btc_tx,btc_price) VALUES ( '$mktcap','$btc1','$eth1','$pct1','$btcvol','$ethvol','$pctvol','$ethprice','$btcrwd','$ethrwd','$pctrwd','$btctx','$btcprice' )");
  $insert->execute();


  /* table 2 api */

  $currencies = array();
  $prices = array();
  $caps = array();

  /* truncate table */
  $truncate_currencies = $app['pdo']->prepare('TRUNCATE currencies');
  $truncate_currencies->execute();

  for ($i = 0; $i < 15; $i++) {
    $currency = $obj[$i]->name;
    $currency_price = $obj[$i]->price_usd;
    $currency_cap = $obj[$i]->market_cap_usd;
    array_push($currencies, $currency);
    array_push($prices, $currency_price);
    array_push($caps, $currency_cap);
  }

  /* insert data into table 2 */
  for ($i = 0; $i < 15; $i++) {
    $table2_insert = $app['pdo']->prepare("INSERT INTO currencies (currency, price, cap) VALUES ( '$currencies[$i]','$prices[$i]','$caps[$i]' ) ");
    $table2_insert->execute();
  }

  /* print results */
  return $mktcap."<br>"
  .$btc1."<br>"
  .$eth1."<br>"
  .$pct1."<br>"
  .$btcvol."<br>"
  .$ethvol."<br>"
  .$pctvol."<br>"
  .$ethprice."<br>"
  .$btcrwd."<br>"
  .$ethrwd."<br>"
  .$pctrwd."<br>"
  .$btctx."<br>"
  .$btcprice."<br><br>"
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

});


/*
$app->get('/script1', function() use($app) {
  return $app['twig']->render('script1.html');
});
*/

$app->get('/ethjsondata', function() use($app) {
  return $app['twig']->render('eth.html');
});

$app->get('/btcjsondata', function() use($app) {
  return $app['twig']->render('btc.html');
});

$app->get('/xrpjsondata', function() use($app) {
  return $app['twig']->render('xrp.html');
});

$app->get('/bchjsondata', function() use($app) {
  return $app['twig']->render('bch.html');
});

$app->get('/mktcapjsondata', function() use($app) {
  return $app['twig']->render('totalmarketcap.html');
});

$app->run();
