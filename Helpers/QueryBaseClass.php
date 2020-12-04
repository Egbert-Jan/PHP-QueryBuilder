<?php

abstract class QueryBaseClass {
    public $table;
    protected $pdo;
    public static $sharedPDO;
    protected $debug = false;

    function __construct($pdo = null)
    {
        $this->pdo = $pdo;
        if(is_null($pdo)) {
            $this->pdo = static::$sharedPDO;
        }
    }

    public function debug() {
        $this->debug = true;
        return $this;
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    protected function printErrorsWhenInDebug($prepared) {
        if($this->debug) {
            echo "<br><b>Query Builder Debug Values:</b><br>";
            print_r($prepared);
            echo "<br>";
            print_r($prepared->errorInfo());
            echo "<br>";
        }
    }

    public function getDriver() {
        return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    // public abstract function exec();
}