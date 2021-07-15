<?php
	include('conn.php'); //create connection
	include('funcdb.php'); //get pso function
	include('function.php'); //get pso function
	include('setting.php'); //load global setting


	for ($ith=0; $ith<=$maxIterasi ; $ith++) { 
		for ($par=1; $par<=$particleNum ; $par++) { 
			for ($idrute=1; $idrute<=$jmldata ; $idrute++) { 
				$q = r_random(4);

				if($init_q0 >= $q){
					$tho = getfreecol('value','tb_phero',' awal='.$idrute.' and akhir=0 ');
					$tho = pow($tho, $a_phero);

					$vis = getfreecol('visibility','tb_jarak_i',' iterasi='.$ith.' and partikel='.$par.' and awal='.$idrute.' and akhir=0 ');
					$vis = pow($vis, $b_vis);

					$vistho = $vis * $tho;
				}
			}
		}
	}
?>