<?php
include('conn.php');

	$maxIterasi = 0; //mulai dari nol
	$particleNum = 10; //mulai dari satu
	$susut = 0.001;
	$maxKnap = 950;

	// $init_ith = 0;
	// $init_par = 10;
	
	//stagnan pada: 
	// $ii_stagnan = 5;
	// $par_stagnan = 10;
	// $next_step = $ii_stagnan + 1;
	
	$a_phero = 0.5;
	$b_visib = 0.3;

	// skenario
	// $init_q0 = 0.3; /*This page loaded in  seconds*/
	$init_q0 = 0.5; /*This page loaded in  seconds*/
	// $init_q0 = 0.7; /*This page loaded in  seconds*/
	// $init_q0 = 0.9; /*This page loaded in  seconds*/

	$w1 = 0.3; //bobot count
	$w2 = 0.3; //bobot freq
	$w3 = 0.4; //bobot size

	$query_cekjmldata = "select distinct(id) from tb_data";
	$exec_query_cekjmldata = mysql_query($query_cekjmldata) or die(mysql_error());
	$jmldata = mysql_num_rows($exec_query_cekjmldata); //jumlah ada ada di sini

	$maxKota = $jmldata;
?>