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

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->get('/update', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('update.twig');
});





// Database connection

$app->get('/hello/', function() use($app) {
    return 'Your number: ';
});

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
$app->get('/db/', function() use($app) {
  $in = $app['pdo']->prepare('CREATE TABLE IF NOT EXISTS test_table (
    id bigserial primary key,
    name int(11) NOT NULL
  )');
  $in->execute();



  $abc = $app['pdo']->prepare('INSERT INTO test_table (name) VALUES (  )');
  $abc->execute();

  $st = $app['pdo']->prepare('SELECT name FROM test_table');
  $st->execute();

  $names = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['name']);
    $names[] = $row;
  }

  return $app['twig']->render('database.twig', array(
    'names' => $names
  ));
});





$app->run();
