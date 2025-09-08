<?php
session_start(); //Optional if you know how to start your session outside this file
function loadClasses($class) {
    $path = "./classes/";
    require_once("{$path}{$class}.php");
}

spl_autoload_register('loadClasses');

//Instantiating the CRUD Class
$aimis = new CRUD("localhost:3316", "root", "", "aimis");

//Making Connection to Database
$conn = $aimis->connect();

/**
 * Using CRUDify add() method to add data to databse
 * $crud->add($conn, 'db_table', $_POST, $format, false)  
 */

