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

?>