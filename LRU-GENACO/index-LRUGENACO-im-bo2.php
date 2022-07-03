<?php
include('conn_bo2.php'); //create connection
include('funcdb_bo2.php'); //DB function

function plot_result($table_sets,$cache_max,$caching_method,$time){
	$hr1 = simplex_res('count(iddata) as res from tbir_reqcache where client="'.$caching_method.'"  and cachesize='.$cache_max.' '); 
	$hr2 = simplex_res('count(iddata) as res from tbir_requests where client="'.$caching_method.'" and cachesize='.$cache_max.'  ');
	$c_hr = $hr1.'/'.$hr2;
	$var_hr = $hr1/$hr2;
	$var_hr = round($var_hr*100,2);

	// DL served by cache
	$DLbycache = "SELECT iddata FROM tbir_reqcache WHERE client='".$caching_method."' and cachesize=".$cache_max." ";
	$result_DLbycache = mysql_query($DLbycache) or die (mysql_error());
	while($row = mysql_fetch_array($result_DLbycache)){
		$elapsed = getfreecol("elapsed",$table_sets," 1 and iddata=".$row[0]." ");
		$mysize = $elapsed;
		$sum += $mysize;
	}
	// DL served by server
	$DLbyserver = simplex_res(" sum(elapsed) from ".$table_sets." ");
	$c_bhr = $sum.'/'.$DLbyserver; 
	$var_bhr = $sum/$DLbyserver;
	$var_bhr = round($var_bhr*100,2);

	insertdata("tbir_summary","source,max_cache,method,c_hr,val_hr,c_bhr,val_bhr,waktu",'"'.$table_sets.'",'.$cache_max.',"'.$caching_method.'","'.$c_hr.'",'.$var_hr.',"'.$c_bhr.'",'.$var_bhr.',"'.$time.'"');

	// echo "Metode=".$caching_method.", proxy=".$proxy.", stats=".$stats.", hr=".$reqcache."/".$request." (".$var_hr."%), bhr=".$var_bhr."%\n";
}

// setting single
$arr_caching_method = array('LRU-GENACO');
// $arr_cache_max = array('3000','6000','9000');

// setting mini
// $arr_cache_max = array('25000','50000','90000','150000','200000','250000','300000');
// setting maxi
// $arr_caching_method = array('LRU','LFU','GDSF','SIZE','GDS','LFUDA','LRUGEN');
$arr_cache_max = array('3000','6000','9000','12000','15000','20000','25000','50000','90000','150000','200000','250000','300000');

foreach($arr_caching_method as $caching_method){
	echo "metode = ".$caching_method."\n";
	// catat waktu mulai
	$start = microtime(true);

	file_put_contents("logs.txt", "");

	simplex('TRUNCATE tb_data');
	simplex('TRUNCATE tb_data_kp');
	simplex('TRUNCATE tb_data_kpsize');
	// simplex('TRUNCATE tbir_proxy');
	// simplex('TRUNCATE tbir_proxy_last');
	// simplex('TRUNCATE tbir_reqcache');
	// simplex('TRUNCATE tbir_requests');
	// simplex('TRUNCATE tbir_stats');	
	// simplex('TRUNCATE tbir_cache_size');
	// simplex('TRUNCATE tb_removed');

	foreach ($arr_cache_max as $cache_max) {
		echo "cache_max = ".$cache_max."\n";
		file_put_contents("logs.txt", "");
		insertdata("tbir_cache_size","newmax",$cache_max);
		simplex('TRUNCATE tb_data');
		simplex('TRUNCATE tb_data_kp');
		simplex('TRUNCATE tb_data_kpsize');
		// simplex('TRUNCATE tbir_proxy');
		// simplex('TRUNCATE tbir_proxy_last');
		// simplex('TRUNCATE tbir_reqcache');
		// simplex('TRUNCATE tbir_requests');
		// simplex('TRUNCATE tbir_stats');	
		// simplex('TRUNCATE tbir_cache_size');
		// $cache_max = 250000; // $caching_method = "LFUDA";

		$client = ''.$caching_method.''; //tb_reqcache dan tb_requests sbg method
		$table = ''.$caching_method.''; //tb_proxy dan tb_stats sbg destination
		$table_sets = "tbir_bo2";

		$query = "select * from ".$table_sets." order by id asc";
		// $query = "select * from ".$table_sets." where id<=10 order by id asc";
		$result = mysql_query($query) or die (mysql_error());

		while($row = mysql_fetch_array($result)){
			$size_iddata = $row['size'];
			$id_req = $row['iddata'];
			$nomor_data = $row['id'];
			$req_time = date('Y-m-d h:i:s');
			$elapsed = $row['elapsed'];

			$curr_capacity = getfreecol("sum(size)","tbir_proxy"," destination='".$table."' and cachesize=".$cache_max." ");
			$tersisa = $cache_max - $curr_capacity;
			echo "data masuk: ".$size_iddata." | tersisa: ".$tersisa." | max:".$cache_max."\n";

			echo "data ke-".$nomor_data." --> ";sleep(0);
			$url = getfreecol("url", $table_sets, " iddata=".$id_req." ");

			if($size_iddata > $cache_max) { echo "[MAX:".$cache_max." KB], skip this files (SIZE:".$size_iddata." KB)--> too big\n"; continue;}
			if($elapsed==0) { echo "[Elapsed:0], skip this files...\n"; continue;}

			// insertdata("tbir_requests","client,destination,iddata,size,req_time",'"'.$client.'",'.'"'.$table.'",'.$id_req.',"'.$size_iddata.'","'.$req_time.'"');
			insertdata("tbir_requests","client,destination,cachesize,iddata,size,req_time",'"'.$client.'",'.'"'.$table_sets.'",'.$cache_max.','.$id_req.',"'.$size_iddata.'","'.$req_time.'"');

			// ambil URLnya
			$search_url = getfreecol("url","tbir_proxy"," url='".$url."' and destination='".$table."' and cachesize=".$cache_max." ");

			if($search_url == ''){
				echo "===> ::data tidak ditemukan:: <===\n<br>";
				// jika tidak ditemukan pada tbir_proxy, maka simpan ID URL dan statsnya
				// sebelum itu, cek spacenya, ready or not
				$timestamp = microtime(true);
				$curr_capacity = getfreecol("sum(size)","tbir_proxy"," destination='".$table."' and cachesize=".$cache_max." ");
				$tersisa = $cache_max - $curr_capacity;

				echo "===> ::cek kapasitas proxy:: <===\n<br>";
				$optimization = "false";

				// ini cuma kriteria apakah bisa masuk tanpa optimasi
				// atau space ready (tersisa > size_iddata)
				if($tersisa > $size_iddata){
					$search_url = "'".$url."'";
					echo "===> space ::READY:: <===\n<br>";

					// $cost = round((2 + ($size_iddata/536)) / $size_iddata,2);
					$cost = 1;
					
					// ambil dulu iddata dengan URL yang sama tsb dari tabel ASALnya
					$get_iddata = $id_req;
					$url = getfreecol("url", $table_sets, " iddata=".$id_req." ");
					// insertdata("tbir_proxy","destination,iddata,url,size,cost,updateAt",'"'.$table.'",'.''.$get_iddata.',"'.$url.'",'.$size_iddata.','.$cost.',"'.$timestamp.'"');
					insertdata("tbir_proxy","destination,cachesize,iddata,url,size,cost,updateAt",'"'.$table.'",'.$cache_max.','.$get_iddata.',"'.$url.'",'.$size_iddata.','.$cost.',"'.$timestamp.'"');

					// update counter untuk ID URL
					$cnt = 1;
					// insertdata("tbir_stats","destination,iddata,cnt",'"'.$table.'",'.'"'.$get_iddata.'",'.$cnt);
					insertdata("tbir_stats","destination,cachesize,iddata,cnt",'"'.$table.'",'.$cache_max.',"'.$get_iddata.'",'.$cnt);

					// $curr_cnt_ori = getfreecol("cnt","tbir_statsori"," iddata=".$get_iddata." and destination='".$table."' ");
					// if(!empty($curr_cnt_ori)){
					// 	$cnt2 = $curr_cnt_ori+1;
					// 	echo "ini ta....";
					// 	updatedata("tbir_statsori"," cnt=".$cnt2." where iddata=".$get_iddata." and destination='".$table."' ");
					// }else{ insertdata("tbir_statsori","destination,iddata,cnt",'"'.$table.'",'.'"'.$get_iddata.'",'.$cnt); }

					insertdata("tb_data",'iddata,size,count,freq',"".$get_iddata.",".$size_iddata.",".$cnt.",".$elapsed);
					// insert jika space:READY
					echo "===> ::cached data inserted:: <===\n<br>";
				}
				else{
					// jika tersisa < size_iddata maka lakukan optimasi
					// echo "data masuk: ".$size_iddata." | tersisa: ".$tersisa." | max:".$cache_max."\n";
					// insertdata("tbir_cache_size","iddata,sizeid,newmax",''.$get_iddata.','.''.$size_iddata.','.($cache_max-$size_iddata));

					echo "===> space ::FULL:: <===\n<br>";
					echo "===> do ::OPTIMIZATION:: <===\n<br>";

					// cek dulu total records tb_data 
					$sumkp = getfreecol("count(iddata)","tbir_proxy"," 1 and destination='".$caching_method."' and cachesize=".$cache_max." ");
					echo "isi data tbir_proxy = ".$sumkp."\n"; 
					// agar kerja GENACO tidak terlalu berat, maka himpunan KP dibatasi
					// yang diambil adalah 10 iddata terbawah berdasarkan recency
					// atau data_kp saat itu jika kumulatif masih <10
					
					if($sumkp <= 20) { 
						echo "OPTIMIZATION by LRU...\n";
						$target_id = getfreecol("iddata","tbir_proxy"," 1 and destination='".$caching_method."' and cachesize=".$cache_max." order by updateAt asc limit 1 ");
						simplex('delete from tbir_stats where iddata='.$target_id.'');
						simplex('delete from tbir_proxy where iddata='.$target_id.'');
					}
					else{
						echo "lebih dari 10\n";sleep(0);
						$limitkp = 20;
						// kosongkan target himpunan kp
						// masukkan himpunan kp ke dalam tb_data_kp untuk dibaca GENACO
						echo "PREPARE: tb_data_kp and tb_data_kpsize ..\n";
						simplex(" TRUNCATE tb_data_kp");
						simplex(" TRUNCATE tb_data_kpsize");
						simplex(" INSERT into tb_data_kp(iddata,size,count,freq) SELECT a.iddata, a.size, b.cnt as count, c.freq FROM tbir_proxy a,tbir_stats b,tb_data c WHERE a.iddata=b.iddata and b.iddata=c.iddata and a.destination='".$caching_method."' and a.cachesize=".$cache_max." ORDER BY updateAt ASC LIMIT ".$limitkp." ");
						echo "DONE: tb_data_kp ..\n";
						// die;
						// total dulu tb_data_kpnya
						// cari selisihnya terhadap data baru yang akan masuk
						$curr_himp_kp = getfreecol('sum(size)','tb_data_kp',' 1 ');
						// echo "sum size tb_data_kp:".$curr_himp_kp."\n";
						// $t_size = $cache_max - $curr_himp_kp;
						// echo "t_size:".$curr_himp_kp."\n";
						$newsize_kp = $curr_himp_kp - $size_iddata;
						echo "curr size setelah seleksi updateAt = ".$curr_himp_kp." | size_iddata=".$size_iddata."\n";
						// total batas atas KP adalah
						// batas ini akan dibaca ACO dan GA
						insertdata("tb_data_kpsize","newmax",$newsize_kp);
					
						// jalankan GENACO
						echo "run: ".$caching_method." ...\n";sleep(0);
					    $ch = curl_init(); 
						$thisurl = 'http://localhost/genaco_gd_bo2/runGENACO.php';
					    curl_setopt($ch, CURLOPT_URL, ''.$thisurl.'');
					    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
					    $output = curl_exec($ch);
					    curl_close($ch);

					    $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
						fwrite($myfile, "\n". $output);
						fclose($myfile);
					    echo $output;

						simplex("delete from tbir_proxy where iddata in (select iddata from tb_removed where stat=0)");
						simplex("delete from tbir_stats where iddata in (select iddata from tb_removed where stat=0)");
						simplex("delete from tb_data where iddata in (select iddata from tb_removed where stat=0)");
						simplex("UPDATE tb_removed set stat='1' where stat='0'");
					}

					// $cost = round((2 + ($size_iddata/536)) / $size_iddata,2);
					$cost = 1;

					$get_iddata = $id_req;
					echo "new cached data ready to insert ...\n";sleep(0);

					$url = getfreecol("url", $table_sets, " iddata=".$id_req." ");
					// insertdata("tbir_proxy","destination,iddata,url,size,cost,updateAt",'"'.$table.'",'.''.$get_iddata.',"'.$url.'",'.$size_iddata.','.$cost.',"'.$timestamp.'"');
					insertdata("tbir_proxy","destination,cachesize,iddata,url,size,cost,updateAt",'"'.$table.'",'.$cache_max.','.$get_iddata.',"'.$url.'",'.$size_iddata.','.$cost.',"'.$timestamp.'"');
					$search_url = "'".$url."'";

					// update counter untuk ID URL
					$cnt = 1;
					// insertdata("tbir_stats","destination,iddata,cnt",'"'.$table.'",'.'"'.$get_iddata.'",'.$cnt);
					insertdata("tbir_stats","destination,cachesize,iddata,cnt",'"'.$table.'",'.$cache_max.',"'.$get_iddata.'",'.$cnt);

					// $curr_cnt_ori = getfreecol("cnt","tbir_statsori"," iddata=".$get_iddata." and destination='".$table."' ");
					// if(!empty($curr_cnt_ori)){
					// 	$cnt2 = $curr_cnt_ori+1;
					// 	echo "ini ta....";
					// 	updatedata("tbir_statsori"," cnt=".$cnt2." where iddata=".$get_iddata." and destination='".$table."' ");
					// }else {insertdata("tbir_statsori","destination,iddata,cnt",'"'.$table.'",'.'"'.$get_iddata.'",'.$cnt);}
					insertdata("tb_data",'iddata,size,count,freq',"".$get_iddata.",".$size_iddata.",".$cnt.",".$elapsed);

					//rest id-AI di tb_data
					simplex("SET  @num := 0;");
					simplex("UPDATE tb_data SET id = @num := (@num+1);");
					simplex("ALTER TABLE tb_data AUTO_INCREMENT =1;");

					echo "===> ::cached data inserted (with optimization):: <===\n<br>";
				}
			}else{
				echo "===> ::data ditemukan:: <===\n<br>";
				echo "===> ::CACHE HIT:: <===\n<br>";

				// ambil dulu iddata dengan URL yang sama tsb dari tabel ASALnya
				$get_iddata = $id_req;
				$search_url = "'".$url."'";
				// $size_iddata = $size_iddata;
				// insertdata("tbir_reqcache","client,destination,iddata,size,req_time",'"'.$client.'",'.'"'.$table.'",'.$get_iddata.',"'.$size_iddata.'","'.$req_time.'"');
				insertdata("tbir_reqcache","client,destination,cachesize,iddata,size,req_time",'"'.$client.'",'.'"'.$table_sets.'",'.$cache_max.','.$get_iddata.',"'.$size_iddata.'","'.$req_time.'"');

				// perbarui: updateAt dan size untuk kepentingan LRU
				echo "===> ::update timestamp and size:: <===\n<br>";
				$timestamp = microtime(true); 
				// updatedata("tbir_proxy"," updateAt='".$timestamp."', size=".$size_iddata." where url='".$url."' and destination='".$table."' ");
				updatedata("tbir_proxy"," updateAt='".$timestamp."', size=".$size_iddata." where url='".$url."' and destination='".$table."' and cachesize=".$cache_max." ");

				//update stats
				echo "===> ::update stats:: <===\n<br>";
				// $curr_cnt = getfreecol("cnt","tbir_stats"," iddata=".$get_iddata." and destination='".$table."' ");
				$curr_cnt = getfreecol("cnt","tbir_stats"," iddata=".$get_iddata." and destination='".$table."' and cachesize=".$cache_max." ");
				// $curr_cnt_ori = getfreecol("cnt","tbir_statsori"," iddata=".$get_iddata." and destination='".$table."' ");

				$cnt = $curr_cnt + 1;
				// $cnt2 = $curr_cnt_ori + 1;
				
				// updatedata("tbir_stats"," cnt=".$cnt." where iddata=".$get_iddata." and destination='".$table."' ");
				updatedata("tbir_stats"," cnt=".$cnt." where iddata=".$get_iddata." and destination='".$table."' and cachesize=".$cache_max." ");
				// updatedata("tbir_statsori"," cnt=".$cnt2." where iddata=".$get_iddata." and destination='".$table."' ");
				updatedata("tb_data",' count='.$cnt.', freq='.$elapsed.', size='.$size_iddata.' WHERE iddata='.$get_iddata.' ');
			}
			echo "*** ---- DONE --- ***\n\n";
		}
		$end = microtime(true);
		$time = number_format(($end - $start), 2);
		plot_result($table_sets,$cache_max,$caching_method,$time);
	}
}

?>