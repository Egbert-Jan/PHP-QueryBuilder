<?php

abstract class QueryBaseClass {
    public $table;
    private $pdo;

    function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // public abstract function exec();
}