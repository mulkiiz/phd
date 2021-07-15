<?php
$min = rand(1,10);
$max = rand(1,10);

echo "min = ".$min." | max = ".$max;
$res = 0;

if($max > $min) { $res = $max - $min ; }

echo "\nres = ".$res;
?>