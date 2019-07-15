<?php 
require_once '../src/Dataset.php';


 $dataset = new Iaga\Dataset('data/am_2019-07-03_2019-07-08_P.dat');

 $dataset->setMetadata(
        'link', 
        array(
             'name' => 'exemple_indice_aa.dat',
             'link' => 'https://raw.githubusercontent.com/terresolide/iaga/master/tests/data/exemple_indice_aa.dat',
        )
 );
 $dataset->addTemporalExtent('2019-07-01', '2019-07-06T12:00:00Z');
 
 header('Content-Type: application/json');
 echo $dataset->toJson();

