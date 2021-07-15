<?php

function insertElit($iterasi,$partikel,$iddata)
{
	// echo "FASE: insertElit\n";
	$prev = $iterasi - 1;
	$posisi = 0;
	$kec = 0;
	
	$query = "select partikel from tb_fitness where iterasi = ".$prev." and ispbest='ya'";
	// echo $query; die;
	$result = mysql_query($query) or die (mysql_error());
	$pbest = mysql_fetch_array($result);

	$stat = getfreecol('stat', 'tb_matriks', 'iterasi='.$prev.' and iddata='.$iddata.' and partikel='.$pbest[0]);

	insertdata('tb_matriks','iterasi,partikel,iddata,posisi,stat,kecepatan',''.$iterasi.','.$partikel.','.$iddata.','.$posisi.','.$stat.','.$kec.'');

	// echo "FASE: END insertElit\n";
}

function transform($iterasi,$partikel,$iddata)
{
	// echo "FASE: insertElit\n";
	// $prev = $iterasi - 1;
	$posisi = 0;
	$kec = 0;
	$iterasi = 0;
	// $query = "select partikel from tb_aco_ispbest where iterasi = 0 and ispbest='ya'";
	$query = "select partikel from tb_aco_fxrute where iterasi = 0 and partikel=".$partikel." ";
	// echo $query; die;
	$result = mysql_query($query) or die (mysql_error());
	$pbest = mysql_fetch_array($result);

	$stat = getfreecol('rute', 'tb_aco_rute', 'iterasi=0 and rute='.$iddata.' and partikel='.$pbest[0]);

	if($stat <> '') {
		$stat_i = 1;
		insertdata('tb_matriks','iterasi,partikel,iddata,posisi,stat,kecepatan',''.$iterasi.','.$partikel.','.$iddata.','.$posisi.','.$stat_i.','.$kec.'');
	}else
	{
		$stat_o = 0;
		insertdata('tb_matriks','iterasi,partikel,iddata,posisi,stat,kecepatan',''.$iterasi.','.$partikel.','.$iddata.','.$posisi.','.$stat_o.','.$kec.'');
	}
	// echo "FASE: END insertElit\n";
}

// function generate_solution(){
// 	// generate daftar solusi tiap kromoson pada tiap iterasi
// 	for ($iterasi=1; $iterasi <= $maxIterasi ; $iterasi++) {
// 		for ($partikel=2; $partikel <= $particleNum ; $partikel++) {
// 			insertdata('tb_solusi','iterasi,partikel',$iterasi.','.$partikel);
// 		}
// 	}

// 	// generate init matriks sesuai dengan jumlah partikel dan data
// 	for ($i = 1; $i <= $particleNum ; $i++) { 
// 		for ($j=1; $j <= $jmldata; $j++) { 
// 			$stat = r_random(3);
// 			if($stat > 0.5) {$stat = 1;}
// 			else {$stat = 0;}
// 			insertdata('tb_matriks','iterasi,partikel,iddata,posisi,stat','0,'.$i.','.$j.','.$stat.','.$stat);
// 		}
// 	}
// }
?>