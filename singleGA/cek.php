<?php
	include('conn.php');
	include('setting.php');
	include('funcdb.php');
	include('function.php');
	include('elitism.php');
	include('crossover.php');

	echo " -- Inisialisasi:MULAI --\n";

	for ($iterasi=2; $iterasi <=2; $iterasi++) {
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
		// setOutFit($iterasi);
	}
	for ($iterasi=2; $iterasi <=2; $iterasi++) {
		for ($partikel=1; $partikel <= $particleNum ; $partikel++) {
			setNormal($iterasi,$partikel);
			setKumulatif($iterasi,$partikel);
			// setCsn($iterasi,$partikel);
		}
	}

	for ($iterasi=2; $iterasi <=2; $iterasi++) {
		for ($partikel=1;  $partikel <= $particleNum ; $partikel++) {
			setCsn($iterasi,$partikel);
		}
	}

	echo "-- Inisialisasi:SELESAI --\n";

	echo "\n(-)(-)FINISHED(-)(-)";
?>