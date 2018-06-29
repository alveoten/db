<?php

namespace Tabusoft\DB;

class DBFactory
{

    static $instances = null;
    static $config = null;


    //public static function pushConfig()

    /**     *
     * @param DBFactoryConfig $config
     * @return DB
     */
    public static function getInstance(DBFactoryConfig $config)
    {

        if (self::$instances[$config->getHash()] === null) {

            self::$instances[$config->getHash()] = new DB(
                $config->getHost(),
                $config->getDatabase(),
                $config->getUsername(),
                $config->getPassword(),
                $config->getPort()
            );
        }
        return self::$instances[$config->getHash()];
    }

    public static function getInstanceByHash($hash)
    {
        if (!isset(self::$instances[$hash])) {
            throw new \Exception("can't find db configuration for this hash: {$hash}");
        }

        return self::$instances[$hash];
    }

    public static function getAllProfiledQuery()
    {
        $queries = [];
        if (is_array(self::$instances)) {
            foreach (self::$instances as $instance) {
                $queries = array_merge($queries, $instance->getProfiledQuery());
            }
        }

        uasort($queries, function ($a, $b) {
            $res = ($a["total_time"] <=> $b["total_time"]);
            if ($res === 1)
                return -1;
            if ($res === -1)
                return 1;
        });

        return $queries;
    }
}