<?php

function executeGA($ith){
	include('conn.php');
	include('setting.php');
	include('funcdb.php');
	include('function.php');
	include('elitism.php');
	include('crossover.php');

	echo " -- reRun(".$ith."):GA Algorithm --\n";

	// insert best kromosom sebagai kromosom ke-1 di iterasi berikutnya
	for ($iterasi=$ith; $iterasi<=$ith; $iterasi++) {
		for ($partikel=1; $partikel <= 1 ; $partikel++) {
			for($iddata=1; $iddata<=$jmldata; $iddata++ ){
				insertElit($iterasi,$partikel,$iddata);
			}
		}
	}

	// siklus GA : seleksi, crossover
	for ($iterasi=$ith; $iterasi<=$ith; $iterasi++) {
		Selection($iterasi);
		for ($partikel=2; $partikel <= $particleNum ; $partikel++) {
			CrossOver($iterasi,$partikel);
		}
		MoveSolution($iterasi);
	}

	// hitung probabilitas mutasi gen dalam kromosom
	for ($iterasi=$ith; $iterasi<=$ith; $iterasi++) {
		for ($partikel=2; $partikel<=$particleNum ; $partikel++) {
			for($iddata=1; $iddata<=$jmldata; $iddata++ ){
				Mutasi($iterasi,$partikel,$iddata);
			}
		}
	}

	// jalankan kembali fungsi fitness
	for ($iterasi=$ith; $iterasi<=$ith; $iterasi++) {
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
	for ($iterasi=$ith; $iterasi<=$ith; $iterasi++) {
		for ($partikel=1; $partikel <= $particleNum ; $partikel++) {
			setNormal($iterasi,$partikel);
			setKumulatif($iterasi,$partikel);
		}
	}

	for ($iterasi=$ith; $iterasi<=$ith; $iterasi++) {
		for ($partikel=1;  $partikel <= $particleNum ; $partikel++) {
			setCsn($iterasi,$partikel);
		}
	}
	setGbest($ith); 
	echo "\n(-)(-)FINISHED(-)(-)";
}
	// for ($i=1; $i<=3; $i++) { 
		// executeGA($i);sleep(10);
	// }
	executeGA(3);
?>