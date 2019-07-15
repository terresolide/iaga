<?php 
require_once '../src/Dataset.php';


/// $dataset = new Iaga\Dataset('data/am_2019-07-03_2019-07-08_P.dat');

 $dataset = new Iaga\Dataset('data/exemple_indice_Qdays_2016-01_D.dat');
 
 // $dataset->addTemporalExtent('2019-07-01', '2019-07-06T12:00:00Z');
 
 header('Content-Type: application/json');
 echo $dataset->toJson();

