<?php
include('conn.php'); //create connection
include('funcdb.php'); //get pso function
include('function.php'); //get pso function
include('setting.php'); //load global setting

function updateVistho($ith,$par,$city,$maxKnap){
	echo "ith=".$ith." | par=".$par." | city=".$city."\n";

	$bobot_in = getfreecol('size','tb_data',' id = '.$city.' ');
	$rem = $maxKnap - $bobot_in;
	updatedata('tb_matriks',' vis=0,tho=0,vistho=0,vistotal=0 WHERE awal='.$city.' and partikel='.$par.' and iterasi='.$ith.' ');

	$query = "select * from tb_data where id not in (select rute from tb_rute WHERE iterasi=".$ith." and partikel=".$par.") ";
	$result = mysql_query($query) or die (mysql_error());
	while($r = mysql_fetch_array($result)){
		$rem1 = round($rem/$r['size'],6);
		// echo "rem=".$rem."\n";
		updatedata('tb_matriks',' vistho='.$rem1.' WHERE awal='.$r[0].' and partikel='.$par.' and iterasi='.$ith.' ');
	}

	$vtotal = getfreecol('sum(vistho)','tb_matriks',' iterasi='.$ith.' and partikel='.$par);
	while($r = mysql_fetch_array($result)){
		$remain1 = $r['vistho'] / $vtotal;
		updatedata('tb_matriks',' vistotal='.$remain1.' WHERE awal='.$r[0].' and partikel='.$par.' and iterasi='.$ith.' ');
	}
}

function bobotKnap($ith,$par){
	$query = "select sum(size) as res from tb_data where id in (select rute from tb_rute where iterasi=".$ith." and partikel=".$par.")";
	// select sum(size) as res from tb_data where id in (select rute from tb_rute where iterasi=0 and partikel=1)
	$result = mysql_query($query);
	$r = mysql_fetch_array($result);
	// echo "berat=".$r[0]."\n";

	return $r['res'];
}

function profitKnap($ith,$par){
	$query = "select sum(bobot) as res from tb_jarak where id in (select rute from tb_rute where iterasi=".$ith." and partikel=".$par.")";
	// select sum(size) as res from tb_data where id in (select rute from tb_rute where iterasi=0 and partikel=1)
	$result = mysql_query($query);
	$r = mysql_fetch_array($result);
	echo "profit=".$r[0]."\n";

	return $r['res'];
}

function inKnap($ith,$par){
	$query = "select count(rute) as res from tb_rute where iterasi=".$ith." and partikel=".$par."";
	// select count(rute) as res from tb_rute where iterasi=0 and partikel=1
	$result = mysql_query($query);
	$r = mysql_fetch_array($result);
	echo "inKnap=".$r[0]."\n";

	return $r['res'];
}

	$ith=$init_ith; $par=$init_par;
	$stack = array();
	for ($idrute=1; $idrute<=20 ; $idrute++) {
		$q = r_random(4);
		// echo $idrute.") ";
		if($init_q0 >= $q){
			$city = getfreecol('awal','tb_matriks',' awal not in (select rute from tb_rute WHERE iterasi='.$ith.' and partikel='.$par.') ORDER BY vistho DESC limit 1');
			// $city_min = getfreecol('min(awal)','tb_matriks',' awal not in (select rute from tb_rute WHERE iterasi='.$ith.' and partikel='.$par.') ORDER BY vistho ASC limit 1');
			// $city = rand($city_min,$city_maks);

			echo "get r=".$q." | vistho= ".$city."\n";
			insertdata('tb_rute','iterasi,partikel,idrute,rute', $ith.','.$par.','.$idrute.','.$city);
		}else
		{
			// echo "Prob: in\n";
			$city = getfreecol('min(awal)','tb_matriks',' awal not in (select rute from tb_rute WHERE iterasi='.$ith.' and partikel='.$par.') LIMIT 1');
			// $city_min = getfreecol('min(awal)','tb_matriks',' awal not in (select rute from tb_rute WHERE iterasi='.$ith.' and partikel='.$par.') ORDER BY vistotal ASC limit 1');
			// array_push($stack, $city_maks);

			// $city = shuffle($stack);

			echo "Prob: in | city= ".$city."\n";
			insertdata('tb_rute','iterasi,partikel,idrute,rute', $ith.','.$par.','.$idrute.','.$city);
		}
			
		$w = bobotKnap($ith,$par);

		if($w > 931) {
			simplex(' delete from tb_rute where iterasi='.$ith.' and partikel='.$par.' order by id desc limit 1 ');
			$c = inKnap($ith,$par);
			$w = bobotKnap($ith,$par);
			$profit = round(profitKnap($ith,$par),3);

			insertdata('tb_fxrute','iterasi,partikel,fxrute,fxbobot,inknap', $ith.','.$par.','.$profit.','.$w.','.$c);
			break; 
		}
	}
	updateVistho($ith,$par,$city,$maxKnap);
?>