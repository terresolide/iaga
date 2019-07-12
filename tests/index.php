<?php 
require_once '../src/Chart.php';


 $chart = new Iaga\Chart('data/am_2019-07-03_2019-07-08_P.dat', array('width' => 600));


 echo $chart->toHTML();

