<?php

function Selection($iterasi)
{
	echo "FASE: Selection iterasi ke-".$iterasi."\n";
	$prev = $iterasi - 1;
	$posisi = 0;
	$kec = 0;

	// die;
	$query = "select * from tb_solusi where iterasi = ".$iterasi." and status <> 'ok' ";
	$result = mysql_query($query) or die (mysql_error());

	while($rr = mysql_fetch_array($result)){
		// echo "ada sekian \n";
		$q = "select * from tb_solusi where iterasi = ".$iterasi." and status='no' ORDER BY partikel ASC limit 2";
		$r = mysql_query($q) or die (mysql_error());

		while($row = mysql_fetch_array($r)){
		// echo "ada kromosom \n";
			for ($i=1; $i<=2 ; $i++) { 
				$r = r_random(4);
				$kro = getfreecol('partikel', 'tb_fitness', 'iterasi='.$prev.' and csn >'.$r.' ORDER BY RAND()');
				if($i==1){
					insertdata('tb_seleksi','iterasi,partikel,bapak',$iterasi.','.$row['partikel'].','.$kro);
				}
				if($i==2){
					updatedata('tb_seleksi',' ibu='.$kro.' WHERE iterasi='.$iterasi.' and partikel='.$row['partikel']);
				}
			}
			updatedata('tb_solusi',' status="ok" WHERE iterasi='.$iterasi.' and partikel='.$row['partikel']);
		}
	}
	echo "FASE: Selection CLOSED\n";
}

function CrossOver($iterasi,$partikel,$probcs)
{
	echo "FASE: CrossOver\n";
	$prev = $iterasi - 1;

	// echo "\nprobcs = ".$probcs; die();
	
	if($probcs == "30"){
		$p1 = "LIMIT 3 OFFSET 0";
		$p2 = "LIMIT 17 OFFSET 3";
	}elseif($probcs == "50"){
		$p1 = "LIMIT 10 OFFSET 0";
		$p2 = "LIMIT 10 OFFSET 10";
	}


	$bapak = getfreecol('bapak', 'tb_seleksi', 'iterasi='.$iterasi.' and partikel='.$partikel);
	$ibu = getfreecol('ibu', 'tb_seleksi', 'iterasi='.$iterasi.' and partikel='.$partikel);

	// potong bagian depan kromosom mulai dari $start
	$query_bapak = "INSERT INTO tb_matriks_t(iterasi,partikel,iddata,stat) 
			  SELECT ".$iterasi.",".$partikel.",iddata,stat FROM tb_matriks where iterasi = ".$prev." and partikel=".$bapak." ".$p1." ";
	// echo $query_bapak."\n";
	$result_bapak = mysql_query($query_bapak) or die (mysql_error());

	$query_ibu = "INSERT INTO tb_matriks_t(iterasi,partikel,iddata,stat) 
			  SELECT ".$iterasi.",".$partikel.",iddata,stat FROM tb_matriks where iterasi = ".$prev." and partikel=".$ibu." ".$p2." ";
	// echo $query_ibu."\n";
	$result_ibu = mysql_query($query_ibu) or die (mysql_error());

	echo "FASE: CrossOver CLOSED\n";
}

function MoveSolution($iterasi)
{
	$query = "INSERT INTO tb_matriks(iterasi,partikel,iddata,stat) 
			  SELECT iterasi,partikel,iddata,stat FROM tb_matriks_t where iterasi=".$iterasi." ";
	$result = mysql_query($query) or die (mysql_error());
}

function Mutasi($iterasi,$partikel,$iddata,$pmutasi)
{
	echo "FASE: Mutasi STARTED\n";
	$r = r_random(4);
	$gen = getfreecol('stat', 'tb_matriks', 'iddata='.$iddata.' and iterasi='.$iterasi.' and partikel='.$partikel);

	// echo "this r=".$r." | this gen=".$pmutasi."\n"; die;
	if($r < $pmutasi){
		
		if($gen == 1) {$genx=0;}
		if($gen == 0) {$genx=1;}

		// echo "1 gen bermutasi dari=".$gen." menjadi=".$genx; 
		// echo " [iterasi=".$iterasi.",partikel=".$partikel."]\n";
		// sleep(1);

		updatedata('tb_matriks',' stat='.$genx.' WHERE iddata='.$iddata.' and iterasi='.$iterasi.' and partikel='.$partikel);
	}

	echo "FASE: Mutasi CLOSED\n";
}

?>