<?php
include('conn.php');

	// $query = "DELETE FROM tb_matriks where id > 10";
	$query = "TRUNCATE tb_matriks";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_fitness";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_seleksi";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_solusi";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_gbest";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_matriks_t";
	$result = mysql_query($query) or die (mysql_error());

	echo "== Data clearing done ==";
?>