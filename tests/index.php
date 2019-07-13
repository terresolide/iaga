<?php 
require_once '../src/Chart.php';


 $chart = new Iaga\Chart('data/Kp_2019-07-05_2019-07-12_Q.dat', array('width' => 800));

$chart->addTemporalExtend('2019-07-01', '2019-07-12T12:00:00Z');
 echo $chart->toHTML();

