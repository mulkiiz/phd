<?php

function emptySolution(){
	include('conn.php');

	$query = "TRUNCATE tb_jarak";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_jarak_i";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_phero";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_matriks";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_rute";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_fxrute";
	$result = mysql_query($query) or die (mysql_error());

	echo "== Data clearing done ==";
}
	emptySolution();
?>