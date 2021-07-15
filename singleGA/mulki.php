<?php
include('conn.php');
include('setting.php');
include('funcdb.php');
include('function.php');
include('elitism.php');
include('crossover.php');

	// $maxIterasi = $maxIterasi;
	// $particleNum = $particleNum;
	
	// initGA hanya dijalankan sekali
	echo " -- Inisialisasi:MULAI --\n";
	$iterasi=0;
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
	setGbest(0);
	echo "-- Inisialisasi:SELESAI --\n";


	echo "-- START:GA Algorithm --\n";

for($ii=1; $ii<= $maxIterasi; $ii++){
	echo "START: iterasi ke-".$ii."\n";
	// insert best kromosom sebagai kromosom ke-1 di iterasi berikutnya
	for ($iterasi=$ii; $iterasi<=$ii; $iterasi++) {
		for ($partikel=1; $partikel <= 1 ; $partikel++) {
			for($iddata=1; $iddata<=$jmldata; $iddata++ ){
				insertElit($iterasi,$partikel,$iddata);
			}
		}
	}

	// siklus GA : seleksi, crossover
	for ($iterasi=$ii; $iterasi<=$ii; $iterasi++) {
		Selection($iterasi);
		for ($partikel=2; $partikel <= $particleNum ; $partikel++) {
			CrossOver($iterasi,$partikel);
		}
		MoveSolution($iterasi);
	}

	// hitung probabilitas mutasi gen dalam kromosom
	for ($iterasi=$ii; $iterasi<=$ii; $iterasi++) {
		for ($partikel=2; $partikel<=$particleNum ; $partikel++) {
			for($iddata=1; $iddata<=$jmldata; $iddata++ ){
				Mutasi($iterasi,$partikel,$iddata);
			}
		}
	}

	// jalankan kembali fungsi fitness
	for ($iterasi=$ii; $iterasi<=$ii; $iterasi++) {
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

	for ($iterasi=$ii; $iterasi<=$ii; $iterasi++) {
		for ($partikel=1; $partikel <= $particleNum ; $partikel++) {
			setNormal($iterasi,$partikel);
			setKumulatif($iterasi,$partikel);
		}
	}

	for ($iterasi=$ii; $iterasi<=$ii; $iterasi++) {
		for ($partikel=1;  $partikel <= $particleNum ; $partikel++) {
			setCsn($iterasi,$partikel);
		}
	}
	setGbest($ii); 
}
	echo "-- END:GA Algorithm --\n";
?>