<?php
include('conn.php'); //create connection
include('funcdb.php'); //get pso function
include('function.php'); //get pso function
include('setting.php'); //load global setting

// generate init matriks sesuai dengan jumlah partikel dan data
echo "City Defined : ".$w1."\n";
// echo "Max Iteration : ".$maxIterasi."\n";
echo "Generating distance ...\n";
echo "Generating pheromone ...\n";

//memastikan solusi awal yang di-generate sesuai dengan jumlah ada
$query_cekjmldata = "select distinct(id) from tb_data";
$exec_query_cekjmldata = mysql_query($query_cekjmldata) or die(mysql_error());
$jmldata = mysql_num_rows($exec_query_cekjmldata); //jumlah ada ada di sini

// generate jarak dan visibility setiap cachedata
for ($i=1; $i <=$jmldata ; $i++) { 
	$size = getfreecol('size', 'tb_data', 'id='.$i);
	$count = getfreecol('count', 'tb_data', 'id='.$i);
	$freq = getfreecol('freq', 'tb_data', 'id='.$i);

	$distance = round(($count * $freq) / $size,4);
	$visibility = round(1/$distance,4);
	insertdata('tb_jarak','awal,akhir,visibility,bobot',$i.',0,'.$distance.','.$visibility);
}

// generate matrikx awal pheromone
for ($i=1; $i <=$jmldata ; $i++) { 
	$size = getfreecol('size', 'tb_data', 'id='.$i);
	$sumsize = getfreecol('sum(size)', 'tb_data', ' 1 ');
	// echo "size=".$size." | sumsize=".$sumsize."\n";

	$count = getfreecol('count', 'tb_data', 'id='.$i);
	$sumcount = getfreecol('sum(count)', 'tb_data', ' 1 ');
	// echo "count=".$count." | sumcount=".$sumcount."\n";

	$freq = getfreecol('freq', 'tb_data', 'id='.$i);
	$sumfreq = getfreecol('sum(freq)', 'tb_data', ' 1 ');
	// echo "freq=".$freq." | sumfreq=".$sumfreq."\n";

	$pheromone = round( ($w1* ($count/$sumcount)) + ($w2* ($freq/$sumfreq)) + ($w3* ($size/$sumsize)), 4);
	$pheromone = 6;
	insertdata('tb_phero','awal,akhir,value',''.$i.',0,'.$pheromone);
}

echo "== Solution was generated ==";
?>