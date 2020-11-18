<?php 

//declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pdo = new PDO('mysql:host=localhost;dbname=DataMapperTest', "root", "root");

abstract class Entity {

    // private $pdo;

    function __construct() {
        // self::$pdo = new PDO('mysql:host=localhost;dbname=DataMapperTest', "root", "root");
        // self::$pdo->setAttribute(PDO::)
        // self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

        // global $pdo;
        // $this->pdo = $pdo;
    }
    
    abstract public static function tableName(): string;

    public static function find(int $id) {
        global $pdo;
        $prepared= $pdo->prepare("SELECT * FROM " . static::tableName() . " WHERE id=?");
        $prepared->execute([$id]);
        return $prepared->fetchObject(get_called_class());
    }

    public function findWhere() {
        // echo "where:" . __CLASS__;
        // echo "where:" . get_class($this);
    }

    public function update($w = NULL) {
        //find by $this->id and update
        
        $where = $w;

        if(is_null($w)) {
            $where = $this->id;
        }

        $object_vars = get_object_vars($this);

        $backupVars = $object_vars;
        unset($object_vars["id"]); 

        // echo $keyString . "<br>";
        // echo $valueString . "<br>";

        $keyString = "";
        foreach($object_vars as $key => $value) {
            $singleKey = $key . "=:" . $key;
            $keyString .= $singleKey . ", ";
        }

        //Remove last comma and space
        $keyString = substr($keyString, 0, -2);

        $sql = "UPDATE " . static::tableName() . " SET " . $keyString . " WHERE id=:id";

        global $pdo;
        $prepared = $pdo->prepare($sql);
        var_dump($backupVars);
        $prepared->execute($backupVars);
    }

    public function save()
    {
        $object_vars = get_object_vars($this);
        $keyString = implode(',', array_keys($object_vars));
        $valueString = ':'.implode(', :', array_keys($object_vars));

        $sql = "INSERT INTO " . static::tableName() . " ($keyString) VALUES ($valueString)";

        global $pdo;
        $prepared = $pdo->prepare($sql);
        echo"vanhier: <br>";
        var_dump($prepared);
        $prepared->execute($object_vars);
    }
}

class User extends Entity {
    public $id;
    public $name;
    public $age;
    public $email;

    public static function tableName(): string {
        return "Users";
    }
}

// $user = new User();
// $user->id = 4;
// $user->name = "egber";
// $user->age = 30;
// $user->email = "email";
// $user->save();

$user = User::find(4);
$user->name = "wow";
$user->update();

// $nUser = new User();
// echo $nUser->findWhere();




echo"<br><br>final<br<br>";
echo "<br>";


interface IQuery {
    public function execute();
}

class WhereClaus {
    public $key;
    public $operator;
    public $value;
    
    //AND - OR
    public $afterCondition = NULL;

    public function __construct($key, $operator, $value) {
        $this->key = $key;
        $this->operator = $operator;
        $this->value = $value;
    }
}

class QueryBuilder implements IQuery {

    private $table;
    private $selection = ["*"];

    private $where = [];

    public function __construct() {

    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }
    
    public function setColumns($selection) {
        $this->selection = $selection;
        return $this;
        // $sql = "SELECT * FROM " . static::tableName() . " WHERE id=?";
    }

    private $lastCondition = NULL;

    // $array = array("foo" => "bar");
    public function where($key, $operator, $value) {

        if(count($this->where) > 0) {
            $lastWhere = array_pop($this->where);
            $lastWhere->afterCondition = $this->lastCondition;;
            array_push($this->where, $lastWhere);
        }
        
        array_push($this->where, new WhereClaus($key, $operator, $value));

        return $this;
    }

    public function and() { $this->lastCondition = "AND"; return $this; }
    public function or() { $this->lastCondition = "OR";  return $this; }
    public function not() { $this->lastCondition = "NOT";  return $this; }

    public function execute() {
        global $pdo;

        $sql = "SELECT " . implode(", ", $this->selection) . " FROM " . $this->table;

        if(!is_null($this->where)) {
            $sql .= " WHERE ";
            for($i = 0; $i < count($this->where); $i++) {
                $sql .= $this->where[$i]->key . $this->where[$i]->operator . $this->where[$i]->value;

                $afterCon = $this->where[$i]->afterCondition;
                if(!is_null($afterCon)) {
                    $sql .= " " . $afterCon . " ";
                }
            }            

            // $pdo->prepare($sql);
            // $prepared = $pdo->prepare("SELECT * FROM " . static::tableName() . " WHERE id=?");
            // $prepared->execute([$id]);
        }

        echo $sql;
    }
}



$builder = new QueryBuilder();
$builder = $builder
    ->table("Users")
    // ->setColumns("name") //Optional moetmet array kunnen
    ->setColumns(["name", "age"])
    ->where("id", "=", 4)
    // ->and()
    // ->where("id", "=", 6)
    ->execute();

