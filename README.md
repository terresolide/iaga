
[![ISGI](https://www7.obs-mip.fr/wp-content-aeris/uploads/sites/4/2019/07/logo_ISGI_2-150x150.png)](http://isgi.unistra.fr/)
[![BCMT](https://www7.obs-mip.fr/wp-content-aeris/uploads/sites/4/2017/12/bcmt-e1562157506384.png)](http://www.bcmt.fr/)

&#x202F;
# Class Iaga 
PHP class to read and to handle iaga file (geomagnetic data format)


## Install
### With composer
In your `composer.json`:
 * Add repository: `https://github.com/terresolide/iaga.git`
 * Add require to: `terresolide/iaga`

```json
   "repositories": [
	{
	    "type": "git",
	    "url": "https://github.com/terresolide/iaga.git"
	}
   ],
   "require": {
        "terresolide/iaga": "^0.3.0"
   },
```

### Without composer


## An example use 
### without composer autoload

```php
  require_once '../src/Dataset.php'
 

  // create iaga Dataset from filepath
  $dataset = new \iaga\Dataset('data/iaga_file.dat');
  
  // add metadata link to download
  $dataset->setMetadata(
        'download', 
        array(
                'name' => 'exemple_indice_aa.dat',
                'link' => 'https://url_to_download'
                )
        );

  // output
  header('Content-Type: application/json');
  echo $dataset->toJson();
```


