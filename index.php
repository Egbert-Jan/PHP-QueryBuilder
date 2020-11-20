<?php 

//declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include "Autoloader.php";

$pdo = new PDO('mysql:host=localhost;dbname=DataMapperTest', "root", "root");

$result = (new SelectQuery($pdo))
    ->table("Users")
    ->where("id", "=", 11)
    ->or()
    ->where("id", "=", 12)
    ->orderBy("Name", ">") 
    ->limit(2, 1)
    ->getAll();

$result = (new QueryBuilder($pdo))
    ->table("Users")
    ->select()
    ->where("id", "=", 11)
    ->or()
    ->where("id", "=", 12)
    ->orderBy("Name", ">") 
    ->limit(2, 1)
    ->getAll();

// echo"<br>";
// echo"<br>";


$result1 = (new InsertQuery($pdo))
    ->table("Users")
    ->insert([
        "name" => "koekoek",
        "age" => 20, 
        "email" => "jow"
    ]);


// $result1 = (new QueryBuilder($pdo))
//     ->table("Users")
//     ->insert([
//         "name" => "koekoek",
//         "age" => 20, 
//         "email" => "jow"
//     ]);

// var_dump($result1);

// echo"<br>";
// echo"<br>";

// (new QueryBuilder())
//     ->table("Users")
//     ->insert([
//         "name" => "Egbert",
//         "age" => 20, 
//         "email" => "jow"
//     ]);

// (new QueryBuilder())
//     ->table("Users")
//     ->select()
//     ->where()
//     ->getAll();

// echo"<br>";
// echo"<br>";

// $builder = new SelectQuery();
// $builder = $builder
//     ->table("Users")
//     ->where("id", "=", 4)
//     ->or()
//     ->where("id", "=", 6)
//     ->orderBy("Name", ">") // < / > or DESC / ASC
//     ->limit(3, 10)
//     ->get();

// echo"<br>";
// echo"<br>";

// $builder = new QueryBuilder();
// $builder = $builder
//     ->select()
//     ->table("Users")
//     ->join("Products", "Products.id", "=", 1)
//     ->join("Category", "Users.id", "=", "Category.user_id")
//     ->setColumns(["Users.name", "Products.name"])
//     ->get();
