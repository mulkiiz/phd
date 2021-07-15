<?php
// include('conn.php');
// include('setting.php'); //load global setting

function getiddata($idpartikel,$iterasi)
{
	$query = "select * from tb_aco_rute where partikel = ".$idpartikel." and iterasi = ".$iterasi;
	// echo $query."\n";
	$result = mysql_query($query) or die(mysql_error());

	while($row = mysql_fetch_array($result)){
		// if($row['stat'] == 1){
			$rowid = $rowid.",".$row['rute'];
		// }
	}
	$lenrow = strlen($rowid);
	$iddata = substr($rowid,1,$lenrow);

	//mysql_close();
	return $iddata;
}

function jumlahtotaldata()
{
	$query_cekjmldata = "select distinct(id) from tb_data";
	$exec_query_cekjmldata = mysql_query($query_cekjmldata) or die(mysql_error());
	$jmldata = mysql_num_rows($exec_query_cekjmldata); //jumlah ada ada di sini

	return $jmldata;
}

function generateFXi($idpartikel, $rowid) {
	echo "\nPartikel (".$idpartikel.") running ... \n";
	echo "rowid in knapsack = {".$rowid."} \n";

	// $count = rowcount($rowid);
	$count = jumlahtotaldata();
	// $count = 20;
	// echo "jumlah total data = ".$count." \n"; die;

	$max_freq = maxmin('max','freq','tb_data');
	$min_freq = maxmin('min','freq','tb_data');
	
	$max_count = maxmin('max','count','tb_data');
	$min_count = maxmin('min','count','tb_data');

	$max_size = maxmin('max','size','tb_data');
	$min_size = maxmin('min','size','tb_data');
	
	$query = "select * from tb_data where id in (".$rowid.")";
	$result = mysql_query($query) or die (mysql_error());

	while($row = mysql_fetch_array($result)){
		$fDfr_normal = normalisasi($row['freq'],$min_freq,$max_freq);
		$all_fDfr_normal += $fDfr_normal;

		$fDcnt_normal = normalisasi($row['count'],$min_count,$max_count);
		$all_fDcnt_normal += $fDcnt_normal;

		$fDsz_normal = normalisasi($row['size'],$min_size,$max_size);
		$all_fDsz_normal += $fDsz_normal;
	}
	
	$fDfr = fxvar($count,$all_fDfr_normal)."\n";
	$fDcnt = fxvar($count,$all_fDcnt_normal)."\n";
	$fDsz = fxvar($count,$all_fDsz_normal)."\n";
	$fxtotal = fxtotal($fDfr,$fDcnt,$fDsz);
	// $fxtotal = $fDsz/$fDfr;

	echo "f(x) optimasi pada partikel(".$idpartikel.") = ".$fxtotal."\n"; 
	// echo "\n"; echo "---------"; echo "\n";
	//mysql_close();
	return $fxtotal;
}

function rowcount($rowid)
{
	$pos = strpos($rowid, ',');

	if($rowid == '') $jml = 0;
	elseif($pos =='') $jml = 1;
	else
	{
		$t = explode(',', $rowid);
		$jml = count($t);
	}

	// if(is_null($jml)) echo "kosong.."; die;

	return $jml;
}

function fxtotal($var1,$var2,$var3)
{
	//$fDfr,$fDcnt,$fDsz
	//setting bobot terlebih dahulu
	//cek rumus fungsinya juga
	//perhatikan urutan argument, berpengaruh terhadap perkalian bobot

	// In this article, after several simulations, it is determined that w1, w2, and w3 are 0.3, 0.3, and 0.4, respectively. The optimization algorithm aims to find a suitable matrix X to minimize the value of fobjective. We want to cache as much data as possible, and these data have large data access counts, high data access frequency, and small data size. Thus, fDC and fDF are inversely proportional to fobjective, and fDS is proportional to fobjective.

	// $w1=0.3; $w2=0.3;
	$w1=0.3; $w2=0.3; $w3=0.4;
	// $fx_optimasi = $w1*(1-$var1) + $w2*(1-$var2);
	$fx_optimasi = ($w1*(1-$var1)) + ($w2*(1-$var2)) + ($w3*$var3);
	// $fx_optimasi = 1 / $fx_optimasi;

	return round($fx_optimasi,4);
}

function maxmin($var,$colname,$table)
{
	$query = "select $var($colname) as res1 from $table";
	// echo $query; die();
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);
	
	//mysql_close();
	return $row['res1'];
}

function normalisasi($val,$min,$max)
{
	// echo "min= ".$min." | max= ".$max." | val= ".$val."\n";
	$normval = ($val - $min) / ($max - $min);
	// echo $min; die;
	return $normval;
}

function generateFxz($iterasi,$partikel){
	$query = "select avg(posisi) as avg from tb_aco_matriks where iterasi = ".$iterasi." and partikel=".$partikel." ";
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	//mysql_close();
	return $row['avg'];
}

function generateBerati($idpartikel, $rowid) {
	$query = "select * from tb_data where id in (".$rowid.")";
	// echo $query;
	$result = mysql_query($query) or die (mysql_error());

	$numrows = mysql_num_rows($result);
	if($numrows == 0) {return 0;}

	while($row = mysql_fetch_array($result)){
		$berat = $row['size'];
		$fx_berat += $berat;
	}

	//mysql_close();
	return $fx_berat;
}

function fxvar($count,$allnorm)
{
	$fxval = round((1/$count) * $allnorm,4);
	// echo $allnorm; die;
	//mysql_close();
	return $fxval;
}

function sigmoid($val)
{
	$exp = 2.718281828459045;
	$add = $val * (-1);
	$sig = 1 / (1+pow($exp,$add));

	return $sig;
}

function setMaxFit($iterasi)
{
	// echo "FASE: setGPbest\n";

	$query = "select iterasi,partikel,fitness from tb_aco_fitness where iterasi = ".$iterasi." ORDER BY fitness DESC limit 1";
	// echo $query."\n";

	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	updatedata('tb_aco_fitness'," ispbest='ya' where iterasi=".$row['iterasi']." and partikel=".$row['partikel']." ");
	//mysql_close();
}

function setMinFit($iterasi)
{
	// echo "FASE: setMinFit\n";

	$query = "select iterasi,partikel,fitness from tb_aco_fitness where iterasi = ".$iterasi." ORDER BY fitness ASC limit 1";
	// echo $query."\n";

	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	updatedata('tb_aco_fitness'," ispbest='no' where iterasi=".$row['iterasi']." and partikel=".$row['partikel']." ");
	//mysql_close();
}

function setOutFit($iterasi)
{
	// echo "FASE: setMinFit\n";

	$query = "select iterasi,partikel from tb_aco_fitness where iterasi = ".$iterasi." and fxberat >1200";
	// echo $query."\n";

	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	updatedata('tb_aco_fitness'," ispbest='out' where iterasi=".$row['iterasi']." and partikel=".$row['partikel']." ");
	//mysql_close();
}

function setNormal($iterasi,$partikel)
{
	// echo "FASE: setMinFit\n";

	$query = "select fitness from tb_aco_fitness where ispbest='no' and iterasi=".$iterasi." ";
	$result = mysql_query($query) or die (mysql_error());
	$minfit = mysql_fetch_array($result);

	$query = "select fitness from tb_aco_fitness where iterasi=".$iterasi." and partikel=".$partikel." ";
	$result = mysql_query($query) or die (mysql_error());
	$fit = mysql_fetch_array($result);
	$fitmin = round($fit[0] - $minfit[0],4);

	updatedata('tb_aco_fitness'," fitmin=".$fitmin." where iterasi=".$iterasi." and partikel=".$partikel." ");
	//mysql_close();
}

function setKumulatif($iterasi,$kota)
{
	// echo "FASE: setMinFit\n";

	if($kota==1) {
		$query = "select vistotal from tb_aco_matriks where awal=".$iterasi." and akhir=".$kota." ";
		$result = mysql_query($query) or die (mysql_error());
		$kum = mysql_fetch_array($result);

		updatedata('tb_aco_matriks'," kum=".$kum[0]." where awal=".$iterasi." and akhir=".$kota." ");
	}else{
		$query = "select vistotal from tb_aco_matriks where awal=".$iterasi." and akhir=".$kota." ";
		$result = mysql_query($query) or die (mysql_error());
		$kum_a = mysql_fetch_array($result);

		$prev = $kota - 1;
		$query = "select kum from tb_aco_matriks where awal=".$iterasi." and akhir=".$prev." ";
		$result = mysql_query($query) or die (mysql_error());
		$kum_b = mysql_fetch_array($result);
		
		$kum_fix = $kum_a[0] + $kum_b[0];
		updatedata('tb_aco_matriks'," kum=".$kum_fix." where awal=".$iterasi." and akhir=".$kota." ");
	}
	//mysql_close();
}

function setCsn($iterasi,$partikel)
{
	// echo "FASE: setMinFit\n";

	$query = "select kum from tb_aco_fitness where iterasi=".$iterasi." and partikel=".$partikel." ";
	$result = mysql_query($query) or die (mysql_error());
	$kum_c = mysql_fetch_array($result);
	// echo "kumc= ".$kum_c[0];die;

	$query = "select kum from tb_aco_fitness where iterasi=".$iterasi." ORDER BY id DESC limit 1";
	$result = mysql_query($query) or die (mysql_error());
	$kum_last = mysql_fetch_array($result);
	// echo "kumLAST= ".$kum_last[0]."\n";

	$csn = round($kum_c[0] / $kum_last[0],4);

	updatedata('tb_aco_fitness'," csn=".$csn." where iterasi=".$iterasi." and partikel=".$partikel." ");
	//mysql_close();
}

function setGbest($ith)
{
	echo "FASE: setGbest\n";
	$query = "select * from tb_aco_fxrute where fxbobot <=950 and iterasi=".$ith." ORDER BY inknap DESC, fxtotal ASC limit 1";
	$result = mysql_query($query) or die (mysql_error());

	$numrows = mysql_num_rows($result);
	if($numrows == 0){
		$query = "select * from tb_aco_fxrute where iterasi=".$ith." ORDER BY fxtotal ASC limit 1";
	}

	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	// simplex("TRUNCATE tb_aco_gbest");
	updatedata('tb_aco_fxrute',' ispbest="ya" where id='.$row[0].' ');
	insertdata('tb_aco_ispbest',"iterasi,partikel,fxrute,fxtotal,fxberat,inknap,ispbest",''.$row['iterasi'].','.$row['partikel'].','.$row['fxrute'].','.$row['fxtotal'].','.$row['fxbobot'].','.$row['inknap'].",'ya'");

	echo "END FASE: setGbest\n";
}

function finalknap($goal)
{
	// echo "FASE: setGbest\n";

	if($goal == 'min') { $prefix = 'ORDER BY fxberat DESC, inknap desc limit 1'; }
	elseif($goal == 'max') { $prefix = 'ORDER BY fxberat DESC, inknap desc limit 1'; }

	$max_knap = 20;
	$query = "select * from tb_aco_pbest where fxberat <= ".$max_knap." $prefix";
	// echo $query;
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);
	//mysql_close();

	echo "\ninknap{".$row['inknap']."} pada iterasi ke-".$row['iterasi']." oleh partikel ke-".$row['partikel'];
}

function r_random($digit) 
{
	$a = "0";
	for ($i=1; $i < $digit; $i++) {
		$a = "0".$a;
	}
	$pembagi = "1".$a;
	$num = rand(pow(10, $digit-1), pow(10, $digit)-1) / $pembagi;

	return $num;
}
?>