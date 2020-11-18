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
$user->age = 90;
$user->update();

// $nUser = new User();
// echo $nUser->findWhere();




echo"<br><br>final<br<br>";
echo "<br>";

class KeyValClaus {
    public $key;
    public $operator;
    public $value;
    
    public function __construct($key, $operator, $value) {
        $this->key = $key;
        $this->operator = $operator;
        $this->value = $value;
    }
}

class WhereClaus extends KeyValClaus {
    //AND - OR
    public $afterCondition = NULL;

    public function __construct($key, $operator, $value) {
        parent::__construct($key, $operator, $value);
    }
}

class JoinWhereClaus extends WhereClaus {
    public $table;

    public function __construct($table, $key, $operator, $value) {
        parent::__construct($key, $operator, $value);
        $this->table = $table;
    }
}

class QueryBuilder {
    
    public function select() {
        return new SelectQuery();
    }

    public function insert() {
        return new InsertQuery();
    }
}

abstract class Query {
    protected $table;

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public abstract function execute();
}

class InsertQuery extends Query {
    // INSERT INTO Customers (CustomerName, City, Country)
    // VALUES ('Cardinal', 'Stavanger', 'Norway');

    public function execute() {
        global $pdo;

        $sql = "INSERT INTO " . $this->table;
        echo $sql;
        // implode(", ", $this->selection) . 
        // $sql = "SELECT " . implode(", ", $this->selection) . " FROM " . $this->table;
    }
}

class SelectQuery extends Query {

    private $selection = ["*"];
    private $where = [];
    private $lastCondition = NULL;
    private $joins = [];
    
    public function setColumns($selection) {
        $this->selection = $selection;
        return $this;
    }

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

    public function count($column = "id") { $this->selection = ["COUNT(" . $column . ")"]; return $this; }
    public function average($column = "id") { $this->selection = ["AVG(" . $column . ")"]; return $this; }
    public function sum($column = "id") { $this->selection = ["SUM(" . $column . ")"]; return $this; }

    public function join($table, $key, $operator, $value) {
        $joinClaus = new JoinWhereClaus($table, $key, $operator, $value);
        array_push($this->joins, $joinClaus);
        return $this;
    }

    public function execute() {
        global $pdo;

        $sql = "SELECT " . implode(", ", $this->selection) . " FROM " . $this->table;

        $joins = $this->joins;
        if(!empty($joins)) {
            for($i = 0; $i < count($joins); $i++) {
                $sql .= " INNER JOIN ";
                $sql .=  $joins[$i]->table . " ON " .$joins[$i]->key . $joins[$i]->operator . $joins[$i]->value;
            }
        }
        
        $where = $this->where;
        if(!empty($where)) {
            $sql .= " WHERE ";
            for($i = 0; $i < count($where); $i++) {
                $sql .= $where[$i]->key . $where[$i]->operator . $where[$i]->value;
                $afterCon = $where[$i]->afterCondition;
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
    ->insert()
    ->table("Users")
    ->execute();

echo"<br>";
echo"Example queries: <br>";
echo "SELECT COUNT(column_name) FROM table_name WHERE condition;";
echo"<br>";
echo "SELECT Orders.OrderID, Customers.CustomerName, Orders.OrderDate FROM Orders INNER JOIN Customers ON Orders.CustomerID = Customers.CustomerID;";



echo"<br>";
echo"<br>";
$builder = new QueryBuilder();
$builder = $builder
    ->select()
    ->table("Users")
    ->where("id", "=", 4)
    ->count()
    ->execute();
echo"<br>";
echo"<br>";
$builder = new QueryBuilder();
$builder = $builder
    ->select()
    ->table("Users")
    ->setColumns(["name", "age"])
    ->where("id", "=", 4)
    ->or()
    ->where("id", "=", 6)
    ->execute();
echo"<br>";
$builder = new QueryBuilder();
$builder = $builder
    ->select()
    ->table("Users")
    ->join("Products", "Users.id", "=", "Products.user_id")
    ->join("Category", "Users.id", "=", "Category.user_id")
    ->setColumns(["User.name", "User.age", "Product.name"])
    ->execute();

echo"<br>";
echo"<br>";


