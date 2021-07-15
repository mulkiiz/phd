<?php
include('conn.php'); //create connection
include('funcdb.php'); //get pso function
include('function.php'); //get pso function
include('setting.php'); //load global setting

// echo "string"; die;
// generate init matriks sesuai dengan jumlah partikel dan data
echo "Particle Defined : ".$particleNum."\n";
echo "Max Iteration : ".$maxIterasi."\n";
echo "Generating solution ...\n";

//memastikan solusi awal yang di-generate sesuai dengan jumlah ada
$query_cekjmldata = "select distinct(id) from tb_data";
$exec_query_cekjmldata = mysql_query($query_cekjmldata) or die(mysql_error());
$jmldata = mysql_num_rows($exec_query_cekjmldata); //jumlah ada ada di sini

// generate daftar solusi tiap kromoson pada tiap iterasi
for ($iterasi=1; $iterasi <= $maxIterasi ; $iterasi++) {
	for ($partikel=2; $partikel <= $particleNum ; $partikel++) {
		insertdata('tb_solusi','iterasi,partikel',$iterasi.','.$partikel);
	}
}

// generate init matriks sesuai dengan jumlah partikel dan data
for ($i = 1; $i <= $particleNum ; $i++) { 
	for ($j=1; $j <= $jmldata; $j++) { 
		$stat = 0;
		insertdata('tb_matriks','iterasi,partikel,iddata,posisi,stat,kecepatan','0,'.$i.','.$j.','.r_random(3).','.$stat.','.r_random(3));
	}
}

$query_matriks = "select distinct(id) from tb_matriks";
$exec_query_matriks = mysql_query($query_matriks) or die(mysql_error());
$jmlmatriksdata = mysql_num_rows($exec_query_matriks); //jumlah ada ada di sini

// inisialisasi knapsack awal, jika rand()>0.5 maka akan dimasukkan ke dalam knapsack
// echo $jmlmatriksdata; die;

for ($i = 1; $i <= $jmlmatriksdata; $i++) {
	$isok = getfreecol('posisi', 'tb_matriks', ' iterasi=0 and id='.$i);
	// echo "posisi = ".$isok."\n";
	if($isok > 0.5) {
		updatedata('tb_matriks', 'stat=1 where id='.$i);
	}
}

echo "== Solution was generated ==";
?>