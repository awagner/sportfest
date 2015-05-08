<?php
ini_set('max_execution_time', '0');
error_reporting(0);

//Pfade
//$wwwroot = 'http://192.168.0.9/sportfest/';
$wwwroot = 'http://localhost/sportfest/';
$wwwincludes = $wwwroot.'includes/';

//Datenbank
$host = "localhost";
$user = "root";
$pass = "Murmel";
$database = "sportfest";

//Session verwenden....
session_start();
$sesskey = session_id();

//datenbank verbindung herstellen.
require_once('database.php');
$db = new database($host, $user, $pass, $database);