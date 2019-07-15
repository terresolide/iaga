<?php 
require_once '../src/Chart.php';


// $chart = new Iaga\Chart('data/exemple_indice_Qdays_2016-01_D.dat', array('width' => 800));

$chart = new Iaga\Chart('data/exemple_indice_CKdays_2000_D.dat', array('width' => 800));


 echo $chart->toHTML();

