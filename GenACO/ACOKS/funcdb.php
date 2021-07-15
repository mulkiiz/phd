<?php
// include('conn.php');

function getfreecol($col, $tbl, $where)
{
	$query = "select $col as res1 from $tbl where $where";
	// echo "".$query."\n";
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);
	//mysql_close();

	return $row['res1'];
}

function insertdata($tabel, $col, $val)
{
	$query = "insert into  $tabel($col) values ($val)";
	// echo $query."\n";
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);
	//mysql_close();

	return $row['res1'];
}

function updatedata($tabel, $where)
{
	$query = "update $tabel set $where";
	// echo $query."\n";
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);
	//mysql_close();
	
	return $row['res1'];
}

function simplex($query)
{
	// echo $query."\n";
	$result = mysql_query($query) or die (mysql_error());
}
?>