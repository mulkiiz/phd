<?php

function emptySolution(){
	include('conn.php');

	echo "== Clearing ACO's database ==";

	$query = "TRUNCATE tb_jarak";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_phero";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_matriks";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_rute";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_fxrute";
	$result = mysql_query($query) or die (mysql_error());

	echo "== Data cleared ==";
}
	emptySolution();
?>