<?php

abstract class QueryBaseClass {
    public $table;
    private $pdo;

    function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    // public abstract function exec();
}