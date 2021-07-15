<?php

function setACObest($ith)
{
	echo "FASE: setGbest\n";
	$query = "select * from tb_aco_fxrute where fxbobot <=1200 and iterasi=".$ith." ORDER BY fxbobot DESC limit 1";
	$result = mysql_query($query) or die (mysql_error());

	$numrows = mysql_num_rows($result);
	if($numrows == 0){
		$query = "select * from tb_aco_fxrute where iterasi=".$ith." ORDER BY fxbobot DESC, inknap DESC limit 1";
	}else{
		$query = "select * from tb_aco_fxrute where fxbobot <=1200 and iterasi=".$ith." ORDER BY fxbobot DESC limit 1";
	}
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	// simplex("TRUNCATE tb_gbest");
	insertdata('tb_aco_gbest',"iterasi,partikel,fxtotal,fxberat,inknap",''.$row['iterasi'].','.$row['partikel'].','.$row['fxtotal'].','.$row['fxberat'].','.$row['inknap']);
	updatedata('tb_aco_fxrute',' ispbest="ya" where id='.$row[0].' ');

	echo "END FASE: setGbest\n";
}

function getdataid($idpartikel,$iterasi)
{
	$query = "select * from tb_aco_rute where partikel = ".$idpartikel." and iterasi = ".$iterasi;
	// echo $query."\n";
	$result = mysql_query($query) or die(mysql_error());

	while($row = mysql_fetch_array($result)){
		// if($row['stat'] == 1){
			$rowid = $rowid.",".$row['rute'];
		// }
	}
	$lenrow = strlen($rowid);
	$iddata = substr($rowid,1,$lenrow);

	//mysql_close();
	return $iddata;
}

function getAlldata()
{
	$query_cekjmldata = "select distinct(id) from tb_data";
	$exec_query_cekjmldata = mysql_query($query_cekjmldata) or die(mysql_error());
	$jmldata = mysql_num_rows($exec_query_cekjmldata); //jumlah ada ada di sini

	return $jmldata;
}

function emptydata(){
	$query = "TRUNCATE tb_aco_matriks";
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
	// select sum(size) as res from tb_data where id in (select rute from tb_rute where iterasi=0 and partikel=1)
	$result = mysql_query($query);
	$r = mysql_fetch_array($result);
	// echo "berat=".$r[0]."\n";

	return $r['res'];
}

function profitKnap($ith,$par){
	$query = "select sum(bobot) as res from tb_aco_jarak where id in (select rute from tb_aco_rute where iterasi=".$ith." and partikel=".$par.")";
	// select sum(size) as res from tb_data where id in (select rute from tb_rute where iterasi=0 and partikel=1)
	$result = mysql_query($query);
	$r = mysql_fetch_array($result);
	echo "profit=".round($r[0],2)."\n";

	return $r['res'];
}

function inKnap($ith,$par){
	$query = "select count(rute) as res from tb_aco_rute where iterasi=".$ith." and partikel=".$par."";
	// select count(rute) as res from tb_rute where iterasi=0 and partikel=1
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
	$query = "select fxbobot as res from tb_aco_fxrute WHERE iterasi=".$ith." and partikel=".$par." order by fxbobot DESC limit 1";
	// select sum(value) as res from tb_phero where id in (select rute from tb_rute WHERE iterasi=0 and partikel=1)
	$result = mysql_query($query) or die (mysql_error());
	$r = mysql_fetch_array($result);
	
	return $r['res'];
}

function profitCurrent($ith,$par){
	$query = "select sum(bobot) as res from tb_aco_jarak where awal in (select rute from tb_aco_rute WHERE iterasi=".$ith." and partikel=".$par.") ";
	// select sum(value) as res from tb_phero where id in (select rute from tb_rute WHERE iterasi=0 and partikel=1)
	$result = mysql_query($query) or die (mysql_error());
	$r = mysql_fetch_array($result);
	
	return round($r['res'],4);
}

?>