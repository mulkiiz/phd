<?php
error_reporting(0);
//definisi koneksi mysql 
$conn = mysql_connect("127.0.0.1","","") or die (mysql_error());
//pilih nama DB
$dbname = mysql_select_db("pso_lru_genaco_bo2") or die (mysql_error());
// $dbname = mysql_select_db("pso_cgaca") or die (mysql_error());

if(!$conn) echo "error connection";
// else echo "connected";
?>