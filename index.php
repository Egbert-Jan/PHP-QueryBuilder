<?php 

//declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pdo = new PDO('mysql:host=localhost;dbname=DataMapperTest', "root", "root");

abstract class Entity {

    // private $pdo;
    // private $queryBuiler;

    function __construct() {
        // self::$pdo = new PDO('mysql:host=localhost;dbname=DataMapperTest', "root", "root");
        // self::$pdo->setAttribute(PDO::)
        // self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

        // global $pdo;
        // $this->pdo = $pdo;

        // $this->queryBuiler = new QueryBuilder();
        // $this->queryBuiler;
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





echo"<br><br>final<br<br>";
echo "<br>";

class KeyValClaus {
    public $key;
    public $operator;
    public $value;
    public $placeholder;

    private $placeholderCounter = 0;
    
    public function __construct($key, $operator, $value) {
        $this->key = $key;
        $this->operator = $operator;
        $this->placeholder = ":".$this->placeholderCounter;
        $this->value = $value;

        $this->placeholderCounter++;
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

    public function execute() {
        global $pdo;

        $sql = "INSERT INTO " . $this->table;
        echo $sql;
        // implode(", ", $this->selection) . 
        // $sql = "SELECT " . implode(", ", $this->selection) . " FROM " . $this->table;
    }
}


//Add support for other joins
//Add support for where and joins in same query
class SelectQuery extends Query {

    private $selection = ["*"];
    private $where = [];
    private $joins = [];
    
    public function setColumns($selection) {
        $this->selection = $selection;
        return $this;
    }

    public function where($key, $operator, $value) {     
        array_push($this->where, new WhereClaus($key, $operator, $value));
        return $this;
    }

    public function and() { 
        $this->addOperator("AND");
        return $this; 
    }
    public function or() { 
        $this->addOperator("OR");
        return $this; 
    }

    public function not() { 
        $this->addOperator("NOT");
        return $this;
    }

    private function addOperator($operator) {
        if(count($this->where) < 1) { return; }
        $this->where[count($this->where)-1]->afterCondition = $operator;
    }
    
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
                $sql .=  $joins[$i]->table . " ON " .$joins[$i]->key . $joins[$i]->operator . $joins[$i]->placeholder;
            }
        }
        
        $where = $this->where;
        if(!empty($where)) {
            $sql .= " WHERE ";
            for($i = 0; $i < count($where); $i++) {
                $sql .= $where[$i]->key . $where[$i]->operator . $where[$i]->placeholder;
                $afterCon = $where[$i]->afterCondition;
                if(!is_null($afterCon)) {
                    $sql .= " " . $afterCon . " ";
                }
            }
        }
        
        echo $sql . "<br>";

        //Een van de twee moet altijd null zijn
        $whereOrJoin = array_merge($where, $joins);
        $prepared = $pdo->prepare($sql);
        foreach ($whereOrJoin as $claus) {
            $prepared->bindValue($claus->placeholder, $claus->value);
        }
        return $prepared->execute();
    }
}




// echo"<br>";
// echo"Example queries: <br>";
// echo "INSERT INTO Customers (CustomerName, City, Country) VALUES ('Cardinal', 'Stavanger', 'Norway');";
// // echo "SELECT COUNT(column_name) FROM table_name WHERE condition;";
// echo"<br>";
// // echo "SELECT Orders.OrderID, Customers.CustomerName, Orders.OrderDate FROM Orders INNER JOIN Customers ON Orders.CustomerID = Customers.CustomerID;";
// $builder = new QueryBuilder();
// $builder = $builder
//     ->insert()
//     ->table("Users")
//     ->execute();

// echo"<br>";
// echo"<br>";
// echo"QueryBuilder queries:";
// echo"<br>";
// $builder = new QueryBuilder();
// $builder = $builder
//     ->select()
//     ->table("Users")
//     ->where("id", "=", 4)
//     // ->count()
//     ->execute();
// echo"<br>";
// echo"<br>";
// echo"<br>";

// $builder = new QueryBuilder();

// $builder = $builder
//     ->select()
//     ->table("Users")
//     // ->setColumns(["name", "age"])
//     ->where("id", "=", 4)
//     ->or()
//     ->where("id", "=", 6)
//     ->execute();
// echo"<br>";
$builder = new QueryBuilder();
$builder = $builder
    ->select()
    ->table("Users")
    ->join("Products", "Products.id", "=", 1)
    // ->join("Category", "Users.id", "=", "Category.user_id")
    ->setColumns(["Users.name", "Products.name"])
    ->execute();

// echo"<br>";
// echo"<br>";

// $builder = (new QueryBuilder())
//     ->select()
//     ->table("Users")
//     ->join("Products", "Products.id", "=", 1)
//     // ->join("Category", "Users.id", "=", "Category.user_id")
//     ->setColumns(["Users.name", "Products.name"])
//     ->execute();


// $builder = new QueryBuilder();
// $builder = $builder
//     ->select()
//     ->table("Users")
//     ->join("Products", "Users.id", "=", 3)
//     // ->join("Category", "Users.id", "=", "Category.user_id")
//     ->setColumns(["User.name", "User.age", "Product.name"])
//     ->execute();
