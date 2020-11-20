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
        $this->value = $value;
        
        $this->placeholder = (strpos($value, ".") !== false)
            ? $value
            : ":".static::$placeholderCounter;

        static::$placeholderCounter++;
    }
}