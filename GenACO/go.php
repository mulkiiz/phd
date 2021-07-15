<?php
include('conn.php');
include('setting.php');
include('funcdb.php');
include('function.php');
include('elitism.php');
include('crossover.php');
include('ACO/acoks.php');
include('ACO/setting.php');

$start = microtime(true);

$ii=14;
echo "Mapping pheromone from GA best_gen, pada ith=".$ii."\n";
mapping_pheromone($ii,$susut);

function mapping_pheromone($ii,$susut){	
	$Pgbest = getfreecol('partikel','tb_gbest',' iterasi='.$ii.' order by inknap desc limit 1 ');
	echo "Pgbest=".$Pgbest; 
	// die;
	
	// mapping update pheromone dari current_gbest GA
	$query = "select iddata from tb_matriks where stat=1 and iterasi=".$ii." and partikel=".$Pgbest." ";
	$result = mysql_query($query) or die (mysql_error());
	while($r = mysql_fetch_array($result)){
		$init_phe = getfreecol('visibility','tb_aco_jarak',' awal='.$r[0].'');
		$curr_ph = getfreecol('value','tb_aco_phero',' awal='.$r[0].' ');

		$new_ph = ((1-$susut) * $init_phe) + $curr_ph;
		updatedata('tb_aco_phero',' value='.$new_ph.' WHERE awal='.$r[0].' ');
	}
}

$end = microtime(true);
$time = number_format(($end - $start), 2);
insertdata('tb_time',"iterasi,waktu",$maxIterasi.','.$time);
?>