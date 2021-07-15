<?php
// include('conn.php');
// include('setting.php'); //load global setting

function getiddata($idpartikel,$iterasi)
{
	$query = "select * from tb_matriks where partikel = ".$idpartikel." and iterasi = ".$iterasi;
	// echo $query."\n";
	$result = mysql_query($query) or die(mysql_error());

	while($row = mysql_fetch_array($result)){
		if($row['stat'] == 1){
			$rowid = $rowid.",".$row['iddata'];
		}
	}
	$lenrow = strlen($rowid);
	$iddata = substr($rowid,1,$lenrow);

	//mysql_close();
	return $iddata;
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
	$fx_optimasi = $w1*(1-$var1) + $w2*(1-$var2) + $w3*$var3;
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
	$query = "select avg(posisi) as avg from tb_matriks where iterasi = ".$iterasi." and partikel=".$partikel." ";
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	//mysql_close();
	return $row['avg'];
}

function generateFXi($idpartikel, $rowid) {
	echo "\nPartikel (".$idpartikel.") running ... \n";
	echo "rowid in knapsack = {".$rowid."} \n";

	// $count = rowcount($rowid);
	$count = getAlldata();
	// echo "jumlah rowid = ".$count."\n";
	// echo "total data = ".getAlldata()."\n"; die;

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

function setMaxFit($ith)
{
	echo "FASE: setMaxFit\n";

	$query = "select * from tb_fitness where fxberat <=950 and iterasi=".$ith." ORDER BY inknap DESC, fxtotal ASC limit 1";
	$result = mysql_query($query) or die (mysql_error());

	$numrows = mysql_num_rows($result);
	if($numrows == 0){
		$query = "select * from tb_fitness where iterasi=".$ith." ORDER BY inknap DESC limit 1";
	}
	// else{
	// 	$query = "select * from tb_fitness where fxberat <=950 and iterasi=".$ith." ORDER BY fxberat DESC limit 1";
	// }
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	updatedata('tb_fitness'," ispbest='ya' where iterasi=".$row['iterasi']." and partikel=".$row['partikel']." ");
	//mysql_close();
}

function setMinFit($ith)
{
	echo "FASE: setMinFit\n";

	$query = "select * from tb_fitness where fxberat <=950 and iterasi=".$ith." ORDER BY inknap ASC, fxtotal DESC limit 1";
	$result = mysql_query($query) or die (mysql_error());

	$numrows = mysql_num_rows($result);
	if($numrows == 0){
		$query = "select * from tb_fitness where iterasi=".$ith." ORDER BY fitness DESC limit 1";
	}
	// else{
	// 	$query = "select * from tb_fitness where fxberat <=950 and iterasi=".$ith." ORDER BY fxberat ASC limit 1";
	// }
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	updatedata('tb_fitness'," ispbest='no' where iterasi=".$row['iterasi']." and partikel=".$row['partikel']." ");
	//mysql_close();
}

function setOutFit($iterasi)
{
	// echo "FASE: setMinFit\n";

	$query = "select iterasi,partikel from tb_fitness where iterasi = ".$iterasi." and fxberat >950";
	// echo $query."\n";

	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	updatedata('tb_fitness'," ispbest='out' where iterasi=".$row['iterasi']." and partikel=".$row['partikel']." ");
	//mysql_close();
}

function setNormal($iterasi,$partikel)
{
	echo "FASE: setNormal\n";

	$query = "select fitness from tb_fitness where ispbest='no' and iterasi=".$iterasi." ";
	$result = mysql_query($query) or die (mysql_error());
	$minfit = mysql_fetch_array($result);

	$query = "select fitness from tb_fitness where iterasi=".$iterasi." and partikel=".$partikel." ";
	$result = mysql_query($query) or die (mysql_error());
	$fit = mysql_fetch_array($result);
	$fitmin = round($fit[0] - $minfit[0],4);

	updatedata('tb_fitness'," fitmin=".$fitmin." where iterasi=".$iterasi." and partikel=".$partikel." ");
	//mysql_close();
}

function setKumulatif($iterasi,$partikel)
{
	echo "FASE: setKumulatif\n";

	if($partikel==1) {
		$query = "select fitmin from tb_fitness where iterasi=".$iterasi." and partikel=".$partikel." ";
		$result = mysql_query($query) or die (mysql_error());
		$kum = mysql_fetch_array($result);

		updatedata('tb_fitness'," kum=".$kum[0]." where iterasi=".$iterasi." and partikel=".$partikel." ");
	}else{
		$query = "select fitmin from tb_fitness where iterasi=".$iterasi." and partikel=".$partikel." ";
		$result = mysql_query($query) or die (mysql_error());
		$kum_a = mysql_fetch_array($result);

		$prev = $partikel - 1;
		$query = "select kum from tb_fitness where iterasi=".$iterasi." and partikel=".$prev." ";
		$result = mysql_query($query) or die (mysql_error());
		$kum_b = mysql_fetch_array($result);
		
		$kum_fix = $kum_a[0] + $kum_b[0];
		updatedata('tb_fitness'," kum=".$kum_fix." where iterasi=".$iterasi." and partikel=".$partikel." ");
	}
	//mysql_close();
}

function setCsn($iterasi,$partikel)
{
	// echo "FASE: setMinFit\n";

	$query = "select kum from tb_fitness where iterasi=".$iterasi." and partikel=".$partikel." ";
	$result = mysql_query($query) or die (mysql_error());
	$kum_c = mysql_fetch_array($result);
	// echo "kumc= ".$kum_c[0];die;

	$query = "select kum from tb_fitness where iterasi=".$iterasi." ORDER BY id DESC limit 1";
	$result = mysql_query($query) or die (mysql_error());
	$kum_last = mysql_fetch_array($result);
	// echo "kumLAST= ".$kum_last[0]."\n";

	$csn = round($kum_c[0] / $kum_last[0],4);

	updatedata('tb_fitness'," csn=".$csn." where iterasi=".$iterasi." and partikel=".$partikel." ");
	//mysql_close();
}

function setGbest($ith)
{
	echo "FASE: setGbest\n";
	$query = "select * from tb_fitness where fxberat <=950 and iterasi=".$ith." ORDER BY inknap DESC, fxtotal ASC limit 1";
	$result = mysql_query($query) or die (mysql_error());

	$numrows = mysql_num_rows($result);
	if($numrows == 0){
		$query = "select * from tb_fitness where iterasi=".$ith." ORDER BY fxtotal DESC limit 1";
	}else{
		$query = "select * from tb_fitness where fxberat <=950 and iterasi=".$ith." ORDER BY fxtotal ASC, inknap DESC limit 1";
	}
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);

	// simplex("TRUNCATE tb_gbest");
	insertdata('tb_gbest',"iterasi,partikel,fxtotal,fxberat,inknap",''.$row['iterasi'].','.$row['partikel'].','.$row['fxtotal'].','.$row['fxberat'].','.$row['inknap']);

	echo "END FASE: setGbest\n";

	return $row['fxtotal'];
}

function finalknap($goal)
{
	// echo "FASE: setGbest\n";

	if($goal == 'min') { $prefix = 'ORDER BY fxberat DESC, inknap desc limit 1'; }
	elseif($goal == 'max') { $prefix = 'ORDER BY fxberat DESC, inknap desc limit 1'; }

	$max_knap = 20;
	$query = "select * from tb_pbest where fxberat <= ".$max_knap." $prefix";
	// echo $query;
	$result = mysql_query($query) or die (mysql_error());
	$row = mysql_fetch_array($result);
	//mysql_close();

	echo "\ninknap{".$row['inknap']."} pada iterasi ke-".$row['iterasi']." oleh partikel ke-".$row['partikel'];
}

function r_random($digit) 
{
	// setiap iterasi punya inisial r, semakin besar iterasi semakin besar r namun tidak sampai melebihi nilai r pada iterasi nol
	$a = "0";
	for ($i=1; $i < $digit; $i++) {
		$a = "0".$a;
	}
	$pembagi = "1".$a;
	$num = rand(pow(10, $digit-1), pow(10, $digit)-1) / $pembagi;

	return $num;
}
?>