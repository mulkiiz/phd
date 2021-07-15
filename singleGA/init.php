<?php
include('conn.php');
include('setting.php');
include('funcdb.php');
include('function.php');

	echo " -- Inisialisasi:MULAI --\n";
	$iterasi=0;
	// echo $iterasi; die;

	for ($iterasi=0; $iterasi <= 0 ; $iterasi++) {
		for ($partikel=1; $partikel <= $particleNum ; $partikel++) {
			$idpartikel = $partikel;
			
			$rowid = getiddata($idpartikel,$iterasi);
			$knap = rowcount($rowid);
			$fxtotal = generateFXi($idpartikel, $rowid);
			$fxberat = generateBerati($idpartikel,$rowid);
			$fitness = $fxtotal;
			echo "knap = ".$knap." | berat = ".$fxberat."\n";

			insertdata('tb_fitness',"iterasi,partikel,fxtotal,fxberat,inknap,fitness,ispbest",''.$iterasi.','.$idpartikel.','.$fxtotal.','.$fxberat.','.$knap.','.$fitness.',""');
		}
		setMaxFit($iterasi);
		setMinFit($iterasi);
	}
	for ($iterasi=0; $iterasi <= 0 ; $iterasi++) {
		for ($partikel=1; $partikel <= $particleNum ; $partikel++) {
			setNormal($iterasi,$partikel);
			setKumulatif($iterasi,$partikel);
		}
	}

	for ($iterasi=0; $iterasi <= 0 ; $iterasi++) {
		for ($partikel=1; $partikel <= $particleNum ; $partikel++) {
			setCsn($iterasi,$partikel);
		}
	}
	// echo $iterasi; die;

	setGbest(0);
	echo "-- Inisialisasi:SELESAI --\n";

?>