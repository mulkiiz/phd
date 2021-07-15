<?php
error_reporting(0);

//definisi koneksi mysql 
$conn = mysql_connect("127.0.0.1","root","") or die (mysql_error());
//pilih nama DB
$dbname = mysql_select_db("pso_acoks") or die (mysql_error());

// if(!$conn) echo "error connection";
// else echo "connected";
?>