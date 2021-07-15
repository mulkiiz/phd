<?php
include('conn.php');
include('setting.php');
include('funcdb.php');
include('function.php');
include('elitism.php');
include('crossover.php');
include('ACO/acoks.php');
include('ACO/setting.php');

$ii=4;
echo "Mapping pheromone from GA best_gen, pada ith=".$ii."\n";
mapping_pheromone($ii);

function mapping_pheromone($ii){	
	// $Pgbest = getfreecol('partikel','tb_gbest',' iterasi='.$ii.' ');
	$Pgbest = 1;
	// mapping update pheromone dari current_gbest GA
	$query = "select iddata from tb_matriks where stat=1 and iterasi=".$ii." and partikel=".$Pgbest." ";
	$result = mysql_query($query) or die (mysql_error());
	while($r = mysql_fetch_array($result)){
		$curr_ph = getfreecol('value','tb_aco_phero',' awal='.$r[0].' ');
		$new_ph = $curr_ph + 0.0001;
		updatedata('tb_aco_phero',' value='.$new_ph.' WHERE awal='.$r[0].' ');
	}
}
?>