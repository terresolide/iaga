<?php 
/**
 * At the entry of your app: include vendor autoloader
 */

try {
	if (!file_exists('../vendor/autoload.php')) {
		throw new Exception('Dependencies managed by Composer missing. Please run "php composer.phar install".');
	}
	require_once '../vendor/autoload.php';
} catch (Exception $e) {
	echo "Error: " . $e->getMessage();
	return;
}


 $dataset = new Iaga\Dataset('data/am_2019-07-03_2019-07-08_P.dat');

 $dataset->setMetadata(
        'link', 
        array(
             'name' => 'exemple_indice_aa.dat',
             'link' => 'https://raw.githubusercontent.com/terresolide/iaga/master/tests/data/exemple_indice_aa.dat',
        )
 );
 header('Content-Type: application/json');
 echo $dataset->toJson();

