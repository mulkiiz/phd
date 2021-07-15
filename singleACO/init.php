<?php
	include('conn.php'); //create connection
	include('funcdb.php'); //get pso function
	include('function.php'); //get pso function
	include('setting.php'); //load global setting

function emptydata(){
	$query = "TRUNCATE tb_matriks";
	$result = mysql_query($query) or die (mysql_error());
}

function initACO($ith,$par,$jmldata) {

	emptydata();

	echo "ith=".$ith." | par=".$par."\n";
	echo "generating thao and heuristic\n";
	for ($idrute=1; $idrute<=$jmldata ; $idrute++) { 
		$tho = getfreecol('value','tb_phero',' awal='.$idrute.' and akhir=0 ');
		$tho = round(pow($tho, $a_phero),4);

		$vis = getfreecol('visibility','tb_jarak',' awal='.$idrute.' and akhir=0 ');
		$vis = round(pow($vis, $b_vis),4);

		$vistho = round($vis * $tho,4);
		insertdata('tb_matriks','iterasi,partikel,awal,akhir,vis,tho,vistho',''.$ith.','.$par.','.$idrute.',0,'.$vis.','.$tho.','.$vistho);
	}
	$vtotal = getfreecol('sum(vistho)','tb_matriks',' iterasi='.$ith.' and partikel='.$par);
	for ($idrute=1; $idrute<=$jmldata ; $idrute++) {
		$vistho = getfreecol('vistho','tb_matriks',' iterasi='.$ith.' and partikel='.$par.' and awal='.$idrute.' and akhir=0 ');
		$vistotal = round($vistho / $vtotal,4) ;
		updatedata('tb_matriks',' vistotal='.$vistotal.' WHERE iterasi='.$ith.' and partikel='.$par.' and awal='.$idrute.' ');
	}
	echo "==== END ====";
}

	$ith=$init_ith; $par=$init_par; $jml=$jmldata;
	initACO($ith,$par,$jml);
?>