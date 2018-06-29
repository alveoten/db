<?php

namespace Tabusoft\DB;


class DBFactoryConfig
{
    private $host;
    private $database;
    private $username;
    private $password;
    private $port;
    private $hash;

    public function __construct($host, $database, $username, $password, $port = 3306)
    {
        $this->host = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
        $this->hash = $this->setHash($host, $database, $port);
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($host, $database, $port)
    {
        return "{$host}|{$database}|{$port}";
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }


}