
[![ISGI](https://www7.obs-mip.fr/wp-content-aeris/uploads/sites/4/2019/07/logo_ISGI_2-150x150.png)](http://isgi.unistra.fr/)
[![BCMT](https://www7.obs-mip.fr/wp-content-aeris/uploads/sites/4/2017/12/bcmt-e1562157506384.png)](http://www.bcmt.fr/)

&#x202F;
# Namespace Iaga 

Contains:
 * **Class Dataset**: PHP class to read and to handle iaga file (geomagnetic data format)
 * **Class Chart**: to build chart 

## Use
 * [Highcharts](https://www.highcharts.com/) a javascript library to build interactive charts
 
## Require
 * PHP >= 5.6 
 
## Install
### With composer
#### In your `composer.json`:
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

#### Then update package  
```
composer update
```
### Without composer
Download the source [iaga.zip](https://github.com/terresolide/iaga/archive/master.zip) and add it to library package.

## Examples 
### Export a iaga file to json (data and metadata)
@see [tests/export-json.php](https://raw.githubusercontent.com/terresolide/iaga/master/tests/export-json.php) or [tests/export-json-autoload.php](https://raw.githubusercontent.com/terresolide/iaga/master/tests/export-json-autoload.php)

```php
  // create iaga Dataset from filepath
  $dataset = new \Iaga\Dataset('data/iaga_file.dat');
  
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


