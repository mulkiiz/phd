<?php
include('conn.php');

	// echo "== Clearing ACO's database ==\n";

	// $query = "TRUNCATE tb_aco_jarak";
	// $result = mysql_query($query) or die (mysql_error());

	// $query = "TRUNCATE tb_aco_phero";
	// $result = mysql_query($query) or die (mysql_error());

	// $query = "TRUNCATE tb_aco_matriks";
	// $result = mysql_query($query) or die (mysql_error());

	// $query = "TRUNCATE tb_aco_rute";
	// $result = mysql_query($query) or die (mysql_error());

	// $query = "TRUNCATE tb_aco_fxrute";
	// $result = mysql_query($query) or die (mysql_error());

	// echo "== Data cleared ==\n\n";

	echo "== Clearing GA's database ==\n";

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

	$query = "TRUNCATE tb_cek";
	$result = mysql_query($query) or die (mysql_error());

	echo "== Data cleared ==";
?>