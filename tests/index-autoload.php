<?php 
try {
    if (!file_exists('../vendor/autoload.php')) {
        throw new Exception('Dependencies managed by Composer missing. Please run "php composer.phar install".');
    }
    require_once '../vendor/autoload.php';
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    return;
}

 $chart = new Iaga\Chart('data/Kp_2019-07-05_2019-07-12_Q.dat', array('width' => 800));

$chart->addTemporalExtend('2019-07-01', '2019-07-12T12:00:00Z');
 echo $chart->toHTML();

