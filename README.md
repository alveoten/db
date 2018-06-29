# Tabusoft/DB

Tabusoft/DB is another DB wrapper to PDO/MySQL.

  - It's simple like PDO
  - Simplify the use of array values
  - Configurable with factory class and configuration

## Installation

You can install it with composer

```sh
$ composer require tabusoft/db
```

## Use
### Direct instance of DB.
You can directly pass an array to query to bind ? parameters.

```php
<?php
$db = new \Tabusoft\DB\DB("localhost", 'db-name', 'username', 'password');
$qres = $db->query("SELECT *
                        FROM table 
                        WHERE c1 = ? 
                            OR c2 IN (?) 
                            OR c3 = ?", 
                    [
                        1, //c1
                        [3,4,5], //c2 parameter extends the placeholder as array
                        1 //c3
                    ] );

echo PHP_EOL."Found: ".$qres->rowCount().PHP_EOL;

foreach($qres as $r){
    dump($r);
}
```

### Use with the factory class
```php 
<?php 
$config = new \Tabusoft\DB\DBFactoryConfig("localhost", 'db-name', 'username', 'password');

$db = \Tabusoft\DB\DBFactory::getInstance($config);
```

