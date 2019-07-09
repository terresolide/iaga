<?php
/**
 * IAGA a class to load and to process iaga file
 * 
 * @license GNU
 * @author epointal
 **/
namespace iaga;

include_once 'Config.php';

class Iaga {
    /** 
     * @var string Geomagnetic code (lowercase) of indice or station like AA or KOU
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
     * Concat data from another file
     * @param Iaga $iaga 
     * @param boolean $force merge data even if they have different processingLevel
     */
    public function concat($iaga, $force=false) {
        // check if it's the same code
        
        // check if dates are contiguous
        
        // check if they have same processing Level and same time resolution
        
        // merge in order the data 
        
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
                    "data"     => $data
            );
        }
        return json_encode($rep, JSON_NUMERIC_CHECK);
    }
    
    /**
     * return a xml string
     * @return string
     */
    public function toXml() {
        // @todo
    }
    
    /**
     * Fill metadata and data from Iaga path or url or ftp url
     * @param string $filename path or url to Iaga file
     */
    private function load ($filepath) {
        $flx = fopen( $filepath, "r");
        $this->read($flx, $filepath);
        fclose($flx);
    }
    
    /**
     * Extract informations and data from the resource
     * to fill the metadata and data
     * @param resource $resource
     * @param string $url the path, url, or ftp path of the file
     */
    private function read($resource, $url) {

        if ($resource === false) {
            $this->error = 'CAN NOT OPEN THE FILE ' . $url;
            return;
        }
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
        $this->metadata->temporalExtent->begin = $this->listDates[0];
        $this->metadata->temporalExtent->end = end($this->listDates);
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
        $this->metadata = new \stdClass();
        // initialize description to empty
        $this->metadata->description = '';
        $this->metadata->temporalExtent = new \stdClass();
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
                $this->code = strtolower($value);
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
            $fields[0] = 'time';
            $this->isDatetime = true;
        }
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
        }
        $end = end($data);
        while($end === '|' || $end === '') {
            array_pop($data);
            $end = end($data);
        }
        array_push($this->listDates, $date);
        array_push($this->data, $data);
    }
   
  
}