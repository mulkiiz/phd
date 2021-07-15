<?php
include('conn.php'); //create connection
include('funcdb.php'); //get pso function
include('function.php'); //get pso function
include('setting.php'); //load global setting

function getAlldata()
{
	$query_cekjmldata = "select distinct(id) from tb_data";
	$exec_query_cekjmldata = mysql_query($query_cekjmldata) or die(mysql_error());
	$jmldata = mysql_num_rows($exec_query_cekjmldata); //jumlah ada ada di sini

	return $jmldata;
}

function emptydata(){
	$query = "TRUNCATE tb_matriks";
	$result = mysql_query($query) or die (mysql_error());
}

function updateVistho($ith,$par,$maxKnap){
	// echo "ith=".$ith." | par=".$par." | city=".$city."\n";
	// // die;

	// $bobot_in = getfreecol('size','tb_data',' id = '.$city.' ');
	// $rem = $maxKnap - $bobot_in;
	// updatedata('tb_matriks',' vis=0,tho=0,vistho=0,vistotal=0 WHERE awal='.$city.' and partikel='.$par.' and iterasi='.$ith.' ');
	simplex(' delete from tb_aco_rute where iterasi='.$ith.' and partikel='.$par.' order by id desc limit 1 ');
	$query = "select * from tb_data where id not in (select rute from tb_aco_rute WHERE iterasi=".$ith." and partikel=".$par.") ";
	$result = mysql_query($query) or die (mysql_error());
	while($r = mysql_fetch_array($result)){
		$rem = $maxKnap - $r['size'];
		$rem1 = round($rem/$r['size'],6);
		// echo "rem=".$rem."\n";
		updatedata('tb_aco_matriks',' vistho='.$rem1.' WHERE awal='.$r[0].' and partikel='.$par.' and iterasi='.$ith.' ');
	}

	$vtotal = getfreecol('sum(vistho)','tb_aco_matriks',' iterasi='.$ith.' and partikel='.$par);
	while($r = mysql_fetch_array($result)){
		$remain1 = $r['vistho'] / $vtotal;
		updatedata('tb_aco_matriks',' vistotal='.$remain1.' WHERE awal='.$r[0].' and partikel='.$par.' and iterasi='.$ith.' ');
	}
}

function bobotKnap($ith,$par){
	$query = "select sum(size) as res from tb_data where id in (select rute from tb_aco_rute where iterasi=".$ith." and partikel=".$par.")";
	// select sum(size) as res from tb_data where id in (select rute from tb_aco_rute where iterasi=0 and partikel=1)
	$result = mysql_query($query);
	$r = mysql_fetch_array($result);
	// echo "berat=".$r[0]."\n";

	return $r['res'];
}

function profitKnap($ith,$par){
	$query = "select sum(bobot) as res from tb_jarak where id in (select rute from tb_aco_rute where iterasi=".$ith." and partikel=".$par.")";
	// select sum(size) as res from tb_data where id in (select rute from tb_aco_rute where iterasi=0 and partikel=1)
	$result = mysql_query($query);
	$r = mysql_fetch_array($result);
	echo "profit=".round($r[0],2)."\n";

	return $r['res'];
}

function inKnap($ith,$par){
	$query = "select count(rute) as res from tb_aco_rute where iterasi=".$ith." and partikel=".$par."";
	// select count(rute) as res from tb_aco_rute where iterasi=0 and partikel=1
	$result = mysql_query($query);
	$r = mysql_fetch_array($result);
	echo "inKnap=".$r[0]."\n";

	return $r['res'];
}

function susutPhero($susut){
	updatedata('tb_aco_phero',' value=round(value-'.$susut.',4) ');
	echo "Pheromone telah susut sebesar=".$susut."\n";
}

function profitBest($ith,$par){
	$query = "select fxbobot as res from tb_fxrute WHERE iterasi=".$ith." and partikel=".$par." order by fxbobot DESC limit 1";
	// select sum(value) as res from tb_phero where id in (select rute from tb_aco_rute WHERE iterasi=0 and partikel=1)
	$result = mysql_query($query) or die (mysql_error());
	$r = mysql_fetch_array($result);
	
	return $r['res'];
}

function profitCurrent($ith,$par){
	$query = "select sum(bobot) as res from tb_jarak where awal in (select rute from tb_aco_rute WHERE iterasi=".$ith." and partikel=".$par.") ";
	// select sum(value) as res from tb_phero where id in (select rute from tb_aco_rute WHERE iterasi=0 and partikel=1)
	$result = mysql_query($query) or die (mysql_error());
	$r = mysql_fetch_array($result);
	
	return round($r['res'],4);
}
// END run.php

// start time
$start = microtime(true);

// clear all data
exec('php ACO/emptySolution.php');

// get total data
$jmldata = getAlldata();

echo "LANGKAH 1: inisialisasi jarak dan pheromone\n";
// generate jarak dan visibility setiap cachedata
for ($i=1; $i <=$jmldata ; $i++) { 
	$size = getfreecol('size', 'tb_data', 'id='.$i);
	$count = getfreecol('count', 'tb_data', 'id='.$i);
	$freq = getfreecol('freq', 'tb_data', 'id='.$i);

	$max_count = maxmin('max','count','tb_data');
	$min_count = maxmin('min','count','tb_data');

	$max_freq = maxmin('max','freq','tb_data');
	$min_freq = maxmin('min','freq','tb_data');

	$max_size = maxmin('max','size','tb_data');
	$min_size = maxmin('min','size','tb_data');

	$fDcc_normal = normalisasi($count,$min_count,$max_count);
	$fDfr_normal = normalisasi($freq,$min_freq,$max_freq);
	$fDsz_normal = normalisasi($size,$min_size,$max_size);

	// echo "normalisasi = ".$fDsz_normal; die;

	$fDcc = fxvar($jmldata,$fDcc_normal);
	$fDfr = fxvar($jmldata,$fDfr_normal);
	$fDsz = fxvar($jmldata,$fDsz_normal);

	$fx = fxtotal($fDcc,$fDfr,$fDsz);

	$visibility = round(($count * $freq) / $size,4);
	$bobotvis = 1 - $fx;
	insertdata('tb_aco_jarak','awal,akhir,visibility,bobot',$i.',0,'.$visibility.','.$bobotvis);
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

	$pheromone = round( ($w1* ($count/$sumcount)) + ($w2* ($freq/$sumfreq)) + ($w3* (1-($size/$sumsize))), 4);
	// $pheromone = 6;
	insertdata('tb_aco_phero','awal,akhir,value',''.$i.',0,'.$pheromone);
}
echo "### END LANGKAH 1 ###\n";

// die;

echo "LANGKAH 2: Running ACO\n";

for ($ith=0; $ith<=$maxIterasi ; $ith++) {
	
	emptydata();

	for ($par=1; $par<=$particleNum ; $par++) {
		echo "iterasi ke--".$ith." | partikel ke--".$par."\n\n"; 
		echo "Sub-1 LANGKAH-2: init ACO\n";
		// echo "ith=".$ith." | par=".$par."\n";
		echo "generating thao and heuristic\n";
		for ($idrute=1; $idrute<=$jmldata ; $idrute++) { 
			$tho = getfreecol('value','tb_aco_phero',' awal='.$idrute.' and akhir=0 ');
			$tho = round(pow($tho, $a_phero),4);

			$vis = getfreecol('visibility','tb_aco_jarak',' awal='.$idrute.' and akhir=0 ');
			$vis = round(pow($vis, $b_vis),4);

			$vistho = round($vis * $tho,4);
			$vistho = $vistho + ($vistho + $vistho);

			insertdata('tb_aco_matriks','iterasi,partikel,awal,akhir,vis,tho,vistho',''.$ith.','.$par.','.$idrute.',0,'.$vis.','.$tho.','.$vistho);
		}

		$vtotal = getfreecol('sum(vistho)','tb_aco_matriks',' iterasi='.$ith.' and partikel='.$par);
		for ($idrute=1; $idrute<=$jmldata ; $idrute++) {
			$vistho = getfreecol('vistho','tb_aco_matriks',' iterasi='.$ith.' and partikel='.$par.' and awal='.$idrute.' and akhir=0 ');
			$vistotal = round($vistho / $vtotal,4) ;
			updatedata('tb_aco_matriks',' vistotal='.$vistotal.' WHERE iterasi='.$ith.' and partikel='.$par.' and awal='.$idrute.' ');
		}
		// die;
		echo "Sub-2 LANGKAH-2: run ACO\n";
		// echo "init_q0=".$init_q0; die;
		
		for ($idrute=1; $idrute<=20 ; $idrute++) {
			$q = r_random(4);
			echo $idrute.") ";
			if($init_q0 >= $q){
				$city = getfreecol('awal','tb_aco_matriks',' awal not in (select rute from tb_aco_rute WHERE iterasi='.$ith.' and partikel='.$par.') ORDER BY vistho DESC Limit 1');

				echo "get r=".$q." | vistho= ".$city."\n";
				insertdata('tb_aco_rute','iterasi,partikel,idrute,rute', $ith.','.$par.','.$idrute.','.$city);
			}else
			{
				$q = rand(0.1,0.8);
				$city = getfreecol('awal','tb_aco_matriks',' vistho > '.$q.' and awal not in (select rute from tb_aco_rute WHERE iterasi='.$ith.' and partikel='.$par.') ORDER BY RAND() Limit 1');

				echo "Prob: in | city= ".$city."\n";
				insertdata('tb_aco_rute','iterasi,partikel,idrute,rute', $ith.','.$par.','.$idrute.','.$city);
			}

			updatedata('tb_aco_matriks',' vis=0,tho=0,vistho=0,vistotal=0 WHERE awal='.$city.' and partikel='.$par.' and iterasi='.$ith.' ');
			$w = bobotKnap($ith,$par);

			if($w > 950) {
				simplex(' delete from tb_aco_rute where iterasi='.$ith.' and partikel='.$par.' order by id desc limit 1 ');
				$c = inKnap($ith,$par);
				$w = bobotKnap($ith,$par);
				$profit = round(profitKnap($ith,$par),3);

				insertdata('tb_aco_fxrute','iterasi,partikel,fxrute,fxbobot,inknap', $ith.','.$par.','.$profit.','.$w.','.$c);
				break;
			}
		}

		$rowid = getiddata($par,$ith);
		$fitness = generateFXi($par,$rowid);
		updatedata('tb_aco_fxrute',' fxtotal='.$fitness.' WHERE iterasi='.$ith.' and partikel='.$par.' ');		
	}

	updateVistho($ith,$par,$city,$maxKnap);
	setGbest($ith);
	echo " Sub-3 LANGKAH-2: update Pheromone\n";
	$ith=$ith; $par=$par; susutPhero($susut);
	for ($idrute=1; $idrute<=$jmldata; $idrute++) {
		$i_tho = 1 - $susut; 

		$thisidrute = getfreecol('rute','tb_aco_rute',' rute='.$idrute.' and iterasi='.$ith.' and partikel='.$par.' ');

		if($thisidrute<>''){
			$tho = getfreecol('value','tb_aco_phero',' awal='.$idrute);
			
			$tho = $i_tho * $tho;

			$p1 = profitBest($ith,$par);
			$p2 = profitCurrent($ith,$par);
			// echo "tho=".$tho." | best=".$p1." | curr=".$p2."\n";
			$px = $p1 - $p2;

			$sig_tho = 1 / (1 + $px);
			$fxtho = round($tho + $sig_tho,4);

			updatedata('tb_aco_phero',' value=round(value+'.$fxtho.',2) WHERE awal='.$idrute);
		}
	}

	if($ith == 5 or $ith == 10 or $ith == 20) {
			$end = microtime(true);
			// $time = number_format(($end - $start), 2);
			$time = round(memory_get_usage(false) / 1024);
			// insertdata('tb_time',"iterasi,waktu",$ith.','.$time);
	}
}	

	// $end = microtime(true);
	// $time = number_format(($end - $start), 2);
	$mempeak = round(memory_get_peak_usage(false) / 1024);
	echo "\nmemory peak = ".$mempeak;
	echo "\n### END ACO ###\n";
	// echo 'Loaded in '.$time.' seconds';
	// insertdata('tb_time',"iterasi,waktu",$maxIterasi.','.$time);
?>