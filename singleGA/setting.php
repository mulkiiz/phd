<?php
	$maxIterasi = 10; //mulai dari nol
	$particleNum = 10; //mulai dari satu

	$query_cekjmldata = "select distinct(id) from tb_data";
	$exec_query_cekjmldata = mysql_query($query_cekjmldata) or die(mysql_error());
	$jmldata = mysql_num_rows($exec_query_cekjmldata); //jumlah ada ada di sini
?>