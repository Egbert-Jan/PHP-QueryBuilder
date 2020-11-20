<?php

class KeyValClause {
    public $key;
    public $operator;
    public $value;
    public $placeholder;

    static $placeholderCounter = 0;
    
    public function __construct($key, $operator, $value) {
        $this->key = $key;
        $this->operator = $operator;
        $this->placeholder = ":".static::$placeholderCounter;
        $this->value = $value;

        static::$placeholderCounter++;
    }
}