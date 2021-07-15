<?php
include('conn.php');
include('setting.php');
include('funcdb.php');
include('function.php');
include('elitism.php');
include('crossover.php');
include('ACO/acoks.php');
include('ACO/setting.php');

// get total data
$jmldata = getAlldata();

$start = microtime(true);

echo "-- START:GA Algorithm --\n";

// generate daftar solusi tiap kromoson pada tiap iterasi
echo "-- Generate:tb_solusi for GA --\n";
for ($iterasi=1; $iterasi <= $maxIterasi ; $iterasi++) {
	for ($partikel=2; $partikel <= $particleNum ; $partikel++) {
		insertdata('tb_solusi','iterasi,partikel',$iterasi.','.$partikel);
	}
}
echo "-- Solution Generated --\n";

echo "Konversi solusi ACO ke GA".$ii."\n";
for ($partikel=1; $partikel <= $particleNum ; $partikel++) {
	for($iddata=1; $iddata<=$jmldata; $iddata++ ){
		transform($iterasi,$partikel,$iddata);
	}
}
echo "-- Konversi SELESAI --\n";

echo "-- Inisialisasi GA: MULAI --\n";
// $iterasi=0;
for ($iterasi=0; $iterasi<=0 ; $iterasi++) {
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
	setMinFit($iterasi);
	setMaxFit($iterasi);
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
echo "### Inisialisasi:SELESAI ###\n";

// die;

for($ii=1; $ii <= $maxIterasi; $ii++){
	echo "Starting GA Algorithm, ith=".$ii."\n";
	// insert best kromosom sebagai kromosom ke-1 di iterasi berikutnya
	for ($iterasi=$ii; $iterasi<=$ii; $iterasi++) {
		for ($partikel=1; $partikel <= $particleNum ; $partikel++) {
			for($iddata=1; $iddata<=$jmldata; $iddata++ ){
				insertElit($iterasi,$partikel,$iddata);
			}
		}
	}
	// die;

	// siklus GA : seleksi, crossover
	for ($iterasi=$ii; $iterasi<=$ii; $iterasi++) {
		Selection($iterasi);
		for ($partikel=2; $partikel <= $particleNum ; $partikel++) {
			CrossOver($iterasi,$partikel);
		}
	}
	
	MoveSolution($iterasi);

	// hitung probabilitas mutasi gen dalam kromosom
	for ($iterasi=$ii; $iterasi<=$ii; $iterasi++) {
		for ($partikel=2; $partikel<=$particleNum ; $partikel++) {
			for($iddata=1; $iddata<=$jmldata; $iddata++ ){
				Mutasi($iterasi,$partikel,$iddata,$pmutasi);
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
	$gbest = setGbest($ii);
	insertdata('tb_cek','iterasi,fxtotal,cnt',$ii.','.$gbest.',1'); 
	
// ============================================================================== //
// = Cek jika solusi GA terjebak pada optimum lokal, 5x gbest dengan nilai sama = //
	if($ii > 1) {
		$ii_prev = $ii - 1;
		$fx_before = getfreecol('fxberat','tb_gbest',' iterasi='.$ii_prev.' ');
		$fx_now = getfreecol('fxberat','tb_gbest',' iterasi='.$ii.' ');

		if($fx_before == $fx_now) { 
			echo "[tb_cek] iterasi ke-".$ii."\n"; 
			echo "\nFASE: updated tb_cek\n"; 
			
			$cnt = getfreecol('cnt','tb_cek',' iterasi='.$ii_prev.' ');
			// $id_cnt = getfreecol('id','tb_cek',' iterasi='.$ii.' ');

			$cnt = $cnt + 1;
			simplex(' TRUNCATE tb_cek ');
			insertdata('tb_cek','iterasi,fxtotal,cnt',$ii.','.$gbest.','.$cnt);
			// updatedata('tb_cek',' cnt='.$cnt.' where id='.$id_cnt.' ');
			// simplex(' DELETE from tb_cek where iterasi='.$ii.' ');
		} else { 
			echo "\nFASE: empty tb_cek\n";
			simplex(' TRUNCATE tb_cek ');
			insertdata('tb_cek','iterasi,fxtotal,cnt',$ii.','.$gbest.',1'); 
		}
	}

	$cnt = getfreecol('cnt','tb_cek',' 1 ');
	if($cnt == 4){
		echo "iterasi ke-".$ii." ---> stagnan\n"; 
		echo "-- END:GA Algorithm --\n";
		break;
		// mapping_pheromone($ii);
		// Harus masuk ke algoritme ACO
		// ambil nilai init phero, kemudian jumlahkan semua fxtotal semua data Gbest yang telah dihasilkan oleh GA
	}
	
// ============================================================================== //
// ============================================================================== //
}

// Jika terjebak pada lokal optimum (stagnan), maka akan dijalankan kembali algoritma ACO //
// ====================================================================================== //
	// file: go.php
// ===================================================================================== //
// ===================================================================================== //	
?>