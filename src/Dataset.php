<?php
/**
 * Dataset a class to load and to process iaga file 
 * 
 * @license CeCILL
 * @author epointal
 **/
namespace Iaga;


class Dataset {
    /** 
     * @var string Geomagnetic code of indice or station like aa or KOU
     */
    protected $code = null;
    
    /**
     * @var string filename
     */
    protected $filename = null;
    
    /**
     * @var Object contains timeResolution, dataType ...
     */
    protected $metadata = null;
    
    /**
     * @var array
     */
    protected  $listDates = array();
    /**
     * @var array 
     */
    protected $data = array();
    
    /**
     * @var array
     */
    protected $fields = array();
    
    /**
     * mostly used when data are limited by a temporal extent
     * by default min = 0 and max is last index of array data
     * @var array of indexmin and indexmax of used data
     */
    protected $extent = array('min' =>null, 'max' =>null);
    
    /**
     * datemin and datemax to limit the data displayed
     *  and to limit or to extend the temporal bounds of the chart
     * @var array $temporalExtent
     */
    protected $temporalExtent = array('min' =>null, 'max' =>null);
    
    /**
     * @var String
     */
    protected $error = null;
    
    /**
     * @var Boolean to know if there are two column for the date
     */
    protected $isDatetime = false;
    
    /**
     * build Iaga from a filepath, http url, or ftp url
     * @param string $filepath
     */
    public function __construct($filepath) {
        $this->load($filepath);
    }
    /**
     * @param string $datemin
     * @param string $datemax
     */
    public function addTemporalExtent($datemin, $datemax = null) {
        if (strlen($datemin)<6) {
            $date0 = null;
        }else if (strlen($datemin) <11 ) {
            // case date without time
            $date0 = $datemin .'T00:00:00.000Z';
        } else if (strlen($datemin) < 20) {
            // case without microtime
            $date0 = substr($datemin, 0, 19) . '.000Z';
        } else {
            $date0 = datemin;
        }
        if (strlen($datemax) < 6 ) {
            $date1 = null;
        }else if (strlen($datemax) <12) {
            // case date without time
            $date1 = substr($datemax, 0, 10) .'T23:59:59.000Z';
        } else if (strlen($datemax) < 21) {
            // case without microtime
            $date1 = substr($datemax, 0, 19) . '.000Z';
        } else {
            $date1 = $datemax;
        }
        
        $this->temporalExtent = array('min' => $date0, 'max' => $date1);
        $this->computeExtend($date0, $date1);
    }

    /**
     * Concat data from another file
     * @param \Iaga\Dataset $dataset 
     * @param boolean $force merge data even if they have different processingLevel
     */
    public function concat($dataset, $force=false) {
        // @todo
        // check if it's the same code
        
        // check if dates are contiguous
        
        // check if they have same processing Level and same time resolution
        
        // merge in order the data 
        
    }
    
    /**
     * Combine the data of this file with data from another file or Iaga\Dataset
     */
    public function combine($dataset) {
        // @todo
    }
    
    /**
     * get code
     * @return string
     */
    public function getCode() {
        return $this->code;
    }
    
    /**
     * get metadata
     * @return Array the array of metadata
     */
    public function getMetadata() {
        return $this->metadata;
    }
    
    /**
     * set metadata
     * @param string $name of metadata
     * @param string $value of metadata
     */
    public function setMetadata($name, $value) {
        $this->metadata->{$name} = $value;
    }
    
    /**
     * get data
     * @return array
     */
    public function getData() {
        return $this->data;
    }
    
    /**
     * Remove the temporal limits or extend
     */
    public function removeTemporalExtent() {
        $this->temporalExtent = array('min' => null, 'max'=> null);
        $this->extent = array('min' => 0, 'max' => count($this->data) - 1);
    }
    
    /**
     * @return a DOMDocument describing metadata and data
     */
    public function toDOMDocument() {
        // @todo
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $msg = $xml->createElement('error', 'Not yet implemented');
        $xml->appendChild($msg);
        return $xml;
    }
    
    /**
     * return a json string from Iaga
     * @return string
     */
    public function toJson() {
        if (!is_null($this->error)) {
            $rep = array("error" => $this->error);
        } else {
            $data = array();
            foreach($this->data as  $values) {
                $data[] = array_combine($this->fields, $values);
            }
            $rep = array(
                    "metadata" => $this->metadata,
                    "data"     => array_slice(
                            $data, 
                            $this->extent['min'], 
                            $this->extent['max'] - $this->extent['min'] + 1
                    )
            );
        }
        return json_encode($rep, JSON_NUMERIC_CHECK);
    }
    
    /**
     * Create a xml from data and metadata
     * @return DOMDocument 
     */
    public function toXml() {
        // @todo
        $xml = $this->toDOMDocument();
        return $xml->saveXML();
    }
    
    private function computeExtend() {
        $min = 0;
        $max = count($this->data) - 1;
        
        if (!is_null($this->temporalExtent['min'])) {
            // Search the first index of data where the date is upper than temporalExtent['min']
            while($this->data[$min][0] < $this->temporalExtent['min'] && $min < $max) {
                $min ++;
            }
        } 
        if (!is_null($this->temporalExtent['max'])) {
            // Search the last index of data where the date is minor than temporalExtent['max']
            while($this->data[$max][0] > $this->temporalExtent['max'] && $min < $max) {
                $max--;
            }
        }
        $this->metadata->temporalExtent['begin'] = $this->data[$min][0];
        $this->metadata->temporalExtent['end']= $this->data[$max][0];
        $this->extent = array( 'min' => $min, 'max' => $max);
    }
    
    /**
     * Fill metadata and data from Iaga path or url or ftp url
     * @param string $filename path or url to Iaga file
     */
    private function load ($filepath) {
        $flx = fopen( $filepath, "r");
        if ($flx === false) {
            $this->error = 'CAN NOT OPEN THE FILE ' . $url;
            return;
        } else {
            $this->read($flx, $filepath);
            fclose($flx);
        }
    }
    
    /**
     * Extract informations and data from the resource
     * to fill the metadata and data
     * @param resource $resource
     * @param string $url the path, url, or ftp path of the file
     */
    private function read($resource, $url) {

        $this->initMetadata($url);
        
        //read line by line the resource
        while (!feof($resource)) {
            $line = fgets($resource);
            // action according the first character of $line
            switch($line[0]) {
            case ' ':
                $this->extractMetadata($line);
                break;
            case 'D':
                $this->extractFields($line);
                break;
            default:
                $this->extractData($line);
                break;
            }
        }
        // order data by date (usefull???)
        // array_multisort($this->listDates, SORT_ASC, SORT_STRING, $this->data);
        $this->computeExtend();
    }
    
    /**
     * Initialize metadata, in particulary with the filename
     * @param string $filepath path, url or ftpurl
     */
    private function initMetadata($filepath) {
        $matches = array();
        preg_match('/[^\/]*$/', $filepath, $matches);
        $this->filename = $matches[0];
        
        $this->metadata = new \stdClass();
        // initialize description to empty
        $this->metadata->description = '';
        $this->metadata->temporalExtent = array();
        $this->metadata->title = $this->filename;
    }
    
    /**
     * Extract name and value of metadata from a line
     * @param string $line
     */
    private function extractMetadata($line) {
        $name = preg_replace('/\s+/','', substr( $line, 1, 23));
        if (strtoupper($name) != 'IAGACODE') {
            $name[0] = strtolower($name[0]);
        } else {
            $name = 'iagaCode';
        }
        if ($name[0] === '#') {
            $this->addLineDescription(substr($line, 2));
        } else {
            $value = substr($line, 24);
            $value = preg_replace('/[\s\|]+/', '', $value);
            if (preg_match('/,{1}/', $value)) {
                $value = preg_split('/,{1}/', $value);
            }
            $this->metadata->{$name} = $value;
            if ($name === 'iagaCode') {
                $this->code = $value;
            }
        }
    }
    
    /**
     * Add line description from a line
     * @param string $line
     */
    private function addLineDescription($line) {
        $this->metadata->description .= preg_replace('/\|+/', '', $line);
    }
    
    /**
     * Extract the field names of data
     * @param string $line
     */
    private function extractFields($line) {
        $fields = preg_split('/\s+/', $line);
        // remove last elements in array if it's space or |
        $end = end($fields);
        while($end === '|' || $end === '') {
            array_pop($fields);
            $end = end($fields);
        }
        if ($fields[0] === 'DATE' && $fields[1] === 'TIME') {
            array_splice($fields, 1, 1 );
            $this->isDatetime = true;
        }
        
        $fields[0] = 'DATETIME';
        $this->fields = $fields;
    }
    
    /**
     * add data from a line
     * @param string $line
     */
    private function extractData($line) {
        if (strlen($line) === 0) {
            // empty line
            return;
        }
        $data = preg_split('/\s+/', $line);
        if ($this->isDatetime) {
            $date = $data[0].'T'.$data[1].'Z';
            array_splice($data, 1, 1);
            $data[0] = $date;
        } else {
            $date = $data[0]. 'T12:00:00.00Z';
        }
        $end = end($data);
        // remove space and other special char extract by split at the end of array
        while($end === '|' || $end === '') {
            array_pop($data);
            $end = end($data);
        }
        array_push($this->listDates, $date);
        array_push($this->data, $data);
    }
   
  
}