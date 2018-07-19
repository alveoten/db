<?php

namespace Tabusoft\DB;

use PDO;

class DB extends PDO
{

    public $_raw_query = [];

    public $_compiled_query = [];

    private $query_debug = [];

    private $events = [];

    const EVENT_PRE_QUERY = "pre_query";
    const EVENT_POST_QUERY = "post_query";

    private $host;
    private $port;
    private $database;

    /**
     * @param string $sql use ? for text, integer, text, array.
     * @param array $values
     * @return PDOStatement
     */
    public function query(string $sql, array $values = [])
    {

       if( isset($this->events[self::EVENT_PRE_QUERY])
           AND $this->events[self::EVENT_PRE_QUERY] instanceof DBEventsQueryInterface ){
           $this->events[self::EVENT_PRE_QUERY]($this, $sql, [
               "values" => $values
           ]);
       }

       $start_time = microtime(true);


       if (count($values) !== 0) {
            $this->expandArray($sql, $values);
            $stm = $this->prepare($sql);
            $index = 0;
            foreach($values as $value){
                $stm->bindValue(++$index, $value, PDO::PARAM_STR);
            }
       } else {
            $compiled_query = $sql;
            $stm = $this->prepare($sql);
        }

        $compiled_query = $sql;


        //@TODO create exception with compiled query and values
        $stm->execute();


        $time = microtime(true) - $start_time;

        $this->pushQueryProfiler($compiled_query, $time);

        $this->_raw_query[] = $sql;
        $this->_compiled_query[] = $compiled_query;

        if( isset($this->events[self::EVENT_POST_QUERY])
            AND $this->events[self::EVENT_POST_QUERY] instanceof DBEventsQueryInterface ){

            $debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,1);

            $this->events[self::EVENT_POST_QUERY]($this, $sql, [
                "values" => $values,
                "compiled_query" => $compiled_query,
                "execution_time" => $time,
                "md5" => md5($sql.$debug[0]["line"].$debug[0]["file"]),
                "line" => $debug[0]["line"],
                "file" => $debug[0]["file"]
            ]);
        }

        return $stm;

    }

    public function addEvent(DBEventsQueryInterface $event, $type){
        $this->events[$type] = $event;
    }

    /**
     * @param $query
     * @param $time
     */
    public function pushQueryProfiler($query, $time)
    {
        $debug = debug_backtrace();
        $hash = md5($query . $debug[1]["file"] . $debug[1]["line"]);


        if (!isset($this->query_debug[$hash])) {
            $this->query_debug[$hash] = [
                "file" => $debug[1]["file"],
                "line" => $debug[1]["line"],
                "sql" => $query,
                "times" => [$time],
                "counter" => 1,
                "total_time" => $time
            ];
        } else {
            $this->query_debug[$hash]["counter"]++;
            $this->query_debug[$hash]["times"][] = $time;
            $this->query_debug[$hash]["total_time"] += $time;
        }

    }

    /**
     * @return array
     */
    public function getProfiledQuery()
    {
        return $this->query_debug;
    }

    private function expandArray(& $sql, & $values){

        $num_of_params_in_sql = $this->getNumOfParamsInQuery($sql);

        if($num_of_params_in_sql !== count($values) ){
            throw new \Exception("The query number of params is different from the number of values passaed");
        }

        $sql_pieces = explode("?", $sql);

        $sql = '';
        $new_values = [];
        foreach($sql_pieces as $i => $s){
            $sql .= $s;
            if(array_key_exists($i,$values) ) {

                if (!is_array($values[$i])) {
                    $sql .= '?';
                    $new_values[] = $values[$i];
                    continue;
                }

                $new_values = array_merge($new_values, $values[$i]);
                $sql .= implode(", ", array_fill(0, count($values[$i]), "?"));
            }
        }

        $values = $new_values;
    }

    private function getNumOfParamsInQuery($sql)
    {
        //remove string comments or name that can use the special PDO ?
        $match = [];
        if( preg_match_all("/`[\w\?]*`/iU",$sql,$match) ){
            foreach($match[0] as $m){
                if( strpos($m,'?' )!== false){
                    throw new \Exception("please avoid expression with ` ? ` into: {$sql}");
                }
            }

        }

        return substr_count($sql,"?");
    }

    public function __construct($host, $database, $username, $password, $port = 3306)
    {
        $dsn = "mysql:host={$host};port={$port};dbname={$database}";
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );

        parent::__construct($dsn, $username, $password, $options);

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host): void
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param mixed $database
     */
    public function setDatabase($database): void
    {
        $this->database = $database;
    }

}
