<?php

namespace Tabusoft\DB\Test;


use PHPUnit\Framework\TestCase;
use Tabusoft\DB\DB;

class TabusoftDBTests extends TestCase
{
    public function testConnection(){
        try {
            $db = new DB("localhost", 'test', 'test', 'test');
        }
        catch (\Exception $e){
            $this->assertFalse(false, "can't connect to db");
        }
    }

    public function testQuery(){
        try {
            $db = new DB("localhost", 'test', 'test', 'test');
        }
        catch (\Exception $e){
            $this->assertFalse(false, "can't connect to db");
        }


        $this->expectOutputString(''); // tell PHPUnit to expect '' as output
        print_r("Hello World");
        $qres = $db->query("SELECT * as `ciao?` FROM tabellaUno WHERE c1 = ?", [1] );

    }



}
