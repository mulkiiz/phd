<?php
include('conn_bo2.php');

	file_put_contents("logs.txt", "");
	
	$query = "TRUNCATE tb_data_kp";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_data_kpsize";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tbir_proxy";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tbir_reqcache";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tbir_requests";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tbir_stats";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tbir_cache_size";
	$result = mysql_query($query) or die (mysql_error());

	$query = "TRUNCATE tb_data";
	$result = mysql_query($query) or die (mysql_error());

	echo "== Data clearing done ==\n";
?>