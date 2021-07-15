<?php

function updateKecepatan($iterasi,$partikel,$iddata){
	// echo 'iddata ke-'.$iddata.' || iterasi = '.$iterasi.' dan partikel ke-'.$partikel."\n";

	// bangkitkan r
	$r1 = r_random(4);
	// tetapkan koef.prob.kawin silang
	// jika ps lebih besar dari r1, maka ambil 2 individu yg akan dikawin silang
	$ps = 0.8;
	// bandingkan dengan csn masing-masing kromosom
	$pPbest = getfreecol('csn', 'tb_fitness', 'iterasi='.$iterasi.' and partikel='.$partikel);


	//definisikan : bobot inersia, r1 dan r2
	// $teta = inersia($iterasi);
	// echo "teta = ".$teta."\n";
	$teta = 1;
	$k = 0.6;
	// $k = 0.4;
	$r1 = r_random(3); $w1 = 1.7;
	$r2 = r_random(3); $w2 = 1.9;

	//cari Pbest dan Gbest pada setiap partikel pada iterasi sebelumnya
	$prev = $iterasi - 1;

	//get pbest dari posisi
	// $pPbest = getfreecol('posisi', 'tb_pbest', 'iterasi='.$prev.' and ispbest = "ya"');
	//get pbest dari fxtotal
	$pPbest = getfreecol('fxtotal', 'tb_pbest', 'iterasi='.$prev.' and ispbest = "ya"');
	
	// $pGbest = setGbest('max');
	// if(empty($pGbest)) $pGbest = getfreecol('fxtotal', 'tb_gbest', ' 1 ');
	$pGbest = getfreecol('fxtotal', 'tb_gbest', ' 1 ');
	
	//cari kecepatan pada iterasi sebelumnya
	
	if($iterasi == 1) {$vnol = r_random(3);}
	else {
		$vnol = getfreecol('kecepatan', 'tb_matriks', 'iterasi='.$prev.' and partikel='.$partikel);
	}
	// $vnol = rand(10,100);

	//cari posisi Pbest dan posisi iterasi saat ini, kurangkanlah !
	if($iterasi == 1) { $pnol = r_random(3); }
	else{
		$pnol = getfreecol('posisi', 'tb_pbest', 'iterasi='.$prev.' and partikel='.$partikel);
	}

	$v1 = $teta * $vnol;
	// echo "v1 = ".$teta." * ".$vnol."\n";

	$v2 = $w1 * $r1 * ($pPbest - $pnol);
	// echo "v2 = ".$r1." * (".$pbest_pos."-".$now_pos.") \n";

	$v3 = $w2 * $r2 * ($pGbest - $pnol);
	// echo "v3 = ".$r2." * (".$gbest_pos."-".$now_pos.") \n";

	$v_next = $k * ($v1+$v2+$v3);
	$v_next = round($v_next,2);
	
	$rand = r_random(3);
	$exp_v = round(sigmoid($v_next),3);

	if($rand > $exp_v) $stat = 1;
	else $stat = 0;

	insertdata('tb_matriks','iterasi,partikel,iddata,posisi,stat,kecepatan',''.$iterasi.','.$partikel.','.$iddata.','.$exp_v.','.$stat.','.$v_next.'');
}
?>