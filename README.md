
[![ISGI](https://www7.obs-mip.fr/wp-content-aeris/uploads/sites/4/2019/07/logo_ISGI_2-150x150.png)](http://isgi.unistra.fr/)
[![BCMT](https://www7.obs-mip.fr/wp-content-aeris/uploads/sites/4/2017/12/bcmt-e1562157506384.png)](http://www.bcmt.fr/)

&#x202F;
# Class Iaga 
PHP class to read and to handle iaga file (geomagnetic data format)


## Install

## An example use

```php
  require_once 'Iaga.php'

  // create Iaga
  $iaga = new Iaga();
  // load file
  $iaga->loadFile('data/iaga_file.dat');
  
  // add metadata link to download
  $iaga->setMetadata(
        'download', 
        array(
                'name' => 'exemple_indice_aa.dat',
                'link' => 'https://url_to_download'
                )
        );

  // output
  header('Content-Type: application/json');
  echo $iaga->toJson();
```


