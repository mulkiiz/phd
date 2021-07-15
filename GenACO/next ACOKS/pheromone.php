<?php
include('conn.php'); //create connection
include('funcdb.php'); //get pso function
include('function.php'); //get pso function
include('setting.php'); //load global setting

function susutPhero($susut){
	updatedata('tb_phero',' value=round(value-'.$susut.',4) ');
	echo "Pheromone telah susut sebesar=".$susut."\n";
}

function profitBest($ith,$par){
	$query = "select fxbobot as res from tb_fxrute WHERE iterasi=".$ith." and partikel=".$par." order by fxbobot DESC limit 1";
	// select sum(value) as res from tb_phero where id in (select rute from tb_rute WHERE iterasi=0 and partikel=1)
	$result = mysql_query($query) or die (mysql_error());
	$r = mysql_fetch_array($result);
	
	return $r['res'];
}

function profitCurrent($ith,$par){
	$query = "select sum(bobot) as res from tb_jarak where awal in (select rute from tb_rute WHERE iterasi=".$ith." and partikel=".$par.") ";
	// select sum(value) as res from tb_phero where id in (select rute from tb_rute WHERE iterasi=0 and partikel=1)
	$result = mysql_query($query) or die (mysql_error());
	$r = mysql_fetch_array($result);
	
	return round($r['res'],4);
}

susutPhero($susut); 
$ith=$init_ith; $par=$init_par;//set terlebih dahulu iterasi dan partikelnya

for($par=$init_par; $par<=$particleNum; $par++){
	for ($idrute=1; $idrute<=$jmldata; $idrute++) {
		$i_tho = 1 - $susut; 

		$thisidrute = getfreecol('rute','tb_rute',' rute='.$idrute.' and iterasi='.$ith.' and partikel='.$par.' ');

		if($thisidrute<>''){
			$tho = getfreecol('value','tb_phero',' awal='.$idrute);
			
			$tho = $i_tho * $tho;

			$p1 = profitBest($ith,$par);
			$p2 = profitCurrent($ith,$par);
			// echo "tho=".$tho." | best=".$p1." | curr=".$p2."\n";
			$px = $p1 - $p2;

			$sig_tho = 1 / (1 + $px);
			$fxtho = round($tho + $sig_tho,4);

			updatedata('tb_phero',' value=round(value+'.$fxtho.',2) WHERE awal='.$idrute);
		}
	}
}

?>