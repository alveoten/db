<?php

use Tabusoft\DB\DB;
use Tabusoft\DB\DBFactoryConfig;

require_once "../vendor/autoload.php";

$db = new DB("localhost", 'test', 'test', 'test');
$qres = $db->query("SELECT 1 as `ciao` FROM tabellaUno WHERE c1 = ? OR c2 IN (?) OR c3 = ?", [1,[3,4,5],1] );

echo PHP_EOL."trovati: ".$qres->rowCount().PHP_EOL;

foreach($qres as $r){
    dump($r);
}


$config = new DBFactoryConfig("localhost","test","test","test");

$db = \Tabusoft\DB\DBFactory::getInstance($config);

$qres = $db->query("select * FROM tabellaDue");
echo PHP_EOL."trovati: ".$qres->rowCount().PHP_EOL;

foreach($qres as $r){
    dump($r);
}