<?php 

//declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include "Autoloader.php";

$pdo = new PDO('mysql:host=localhost;dbname=DataMapperTest', "root", "root");

// $result = (new SelectQuery($pdo))
//     ->table("Users")
//     ->where("id", "=", 11)
//     ->or()
//     ->where("id", "=", 12)
//     ->orderBy("Name", ">") 
//     ->limit(2, 1)
//     ->getAll();

// $result1 = (new InsertQuery($pdo))
//     ->table("Users")
//     ->insert([
//         "name" => "koekoek",
//         "age" => 20, 
//         "email" => "jow"
//     ]);

// $builder = (new SelectQuery($pdo))
//     ->table("Users")
//     ->join("Products", "Products.id", "=", 1)
//     ->join("Category", "Users.id", "=", "Category.user_id")
//     ->setColumns(["Users.name", "Products.name"])
//     ->get();
