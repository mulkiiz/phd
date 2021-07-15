<?php

function emptySolution(){
	include('conn.php');

	$query = "TRUNCATE tb_aco_jarak";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_aco_ispbest";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_aco_phero";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_aco_matriks";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_aco_rute";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_aco_fxrute";
	$result = mysql_query($query) or die (mysql_error());

	// $query = "TRUNCATE tb_aco_time";
	// $result = mysql_query($query) or die (mysql_error());

	echo "== Data clearing done ==";
}
	emptySolution();
?>