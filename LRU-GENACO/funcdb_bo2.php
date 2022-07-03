<?php
include('conn_bo2.php');

function getfreecol($col, $tbl, $where)
{
	$query = "select $col as res1 from $tbl where $where";
	echo $query."\n<br>";
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	return $row['res1'];
}

function insertdata($tabel, $col, $val)
{
	$query = "insert into  $tabel($col) values ($val)";
	echo $query."\n<br>";
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	return $row['res1'];
}

function updatedata($tabel, $where)
{
	$query = "update $tabel set $where";
	echo $query."\n<br>";
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	return $row['res1'];
}

function simplex($query)
{
	echo $query."\n<br>";
	$result = mysql_query($query) or die (mysql_error());
}

function simplex_res($query)
{
	$query = "select ".$query;
	echo $query."\n<br>";
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	return $row[0];
}

function cnt_data($col,$tabel)
{
	$query = "select count(".$col.") as res1 from ".$tabel."";
	// echo $query."\n<br>";
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	return $row['res1'];
}
?>