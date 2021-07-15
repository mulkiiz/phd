<?php
	$maxIterasi = 0; //mulai dari nol
	$particleNum = 3; //mulai dari satu

	// prosentase crossover parent 1
	// $probcs = "30"; // artinya p1:30, p2:70
	$probcs = "50"; // artinya p1:50, p2:50
	
	// skenario
	// $pmutasi=0.12; /* This page loaded in 8.31 seconds */
	// $pmutasi=0.25; /* This page loaded in 11.22 seconds */
	$pmutasi=0.5; /* This page loaded in 11.67 seconds */

	$query_cekjmldata = "select distinct(id) from tb_data";
	$exec_query_cekjmldata = mysql_query($query_cekjmldata) or die(mysql_error());
	$jmldata = mysql_num_rows($exec_query_cekjmldata); //jumlah ada ada di sini
?>