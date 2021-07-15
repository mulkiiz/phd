<?php
include('conn.php');

	$maxIterasi = 20; //mulai dari nol
	$particleNum = 10; //mulai dari satu
	// $susut = 0.1;
	// $maxKnap = 1200;

	// $init_ith = 0;
	// $init_par = 5;

	// $a_phero = 0.5;
	// $b_visib = 0.3;

	// skenario
	// $init_q0 = 0.9; /*This page loaded in 41.56 47.13 seconds*/
	// $init_q0 = 0.7; /*This page loaded in 40.99 40.87 seconds*/
	// $init_q0 = 0.3; /*This page loaded in 34.22 36.41 32.81 seconds*/

	// $w1 = 0.3; //bobot count
	// $w2 = 0.3; //bobot freq
	// $w3 = 0.4; //bobot size

	$query_cekjmldata = "select distinct(id) from tb_data";
	$exec_query_cekjmldata = mysql_query($query_cekjmldata) or die(mysql_error());
	$jmldata = mysql_num_rows($exec_query_cekjmldata); //jumlah ada ada di sini

	$maxKota = $jmldata;
?>