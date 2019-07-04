<?php

/**
 * IAGA
 * @license GNU
 * @author epointal
 **/

class Iaga {
    /** 
     * @var string Geomagnetic code (lowercase) of indice or station like AA or KOU
     */
    private $identifier = null;
    
    /**
     * @var array associative containing timeResolution, title, bbox, temporalExtent, processingLevel ...
     */
    private $metadata = array();
    
    /**
     * @var array 
     */
    private $data = array();
    
    /**
     * @var array
     */
    private $fields = array();
    
    /**
     * @var String
     */
    private $error = null;
    
    /**
     * @var Boolean to know if there are two column for the date
     */
    private $isDatetime = false;
    
    public function __construct() {
    }
    
    /**
     * Fill metadata and data from Iaga path or url
     * @param string $filename path or url to Iaga file
     */
    public function loadFile ($filename) {
        $flx = fopen( $filename, "r");
        if (! $flx === false) {
            $this->read($flx);
        } else {
        	$this->error = 'CAN NOT OPEN THE FILE ' . $filename;
            throw new Exception('CAN NOT OPEN THE FILE ' . $filename);
        }
        fclose($flx);
    }
    
    /**
     * Fill metadata and data from Iaga using ftp 
     * @todo
     */
    public function loadFromFtp($ftp) {
        
    }
    
    /**
     * Concat data from another file
     * @param \Iaga $iaga 
     * @param boolean $force merge data even if they have different processingLevel
     */
    public function concat($iaga, $force=false) {
        // check if it's the same code
        
        // check if dates are contiguous
        
        // check if they have same processing Level and same time resolution
        
        // merge in order the data 
        
    }
    
    /**
     * get identifier
     * @return string
     */
    public function getIdentifier() {
        return $this->identifier;
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
    	$this->metadata[$name] = $value;
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
        $rep = array(
                "metadata" => $this->metadata,
                "data"     => $this->data
        );
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
     * return a json string ready to build graph with style
     * @return string
     */
    public function toJsonGraph() {
        $rep = [];
        // @todo
        return json_encode($rep, JSON_NUMERIC_CHECK);
    }
    
    /**
     * Extract informations and data from the resource
     * to fill the metadata and data
     * @param resource $resource
     */
    private function read($resource) {
    	// initialize description to empty
    	$this->metadata['description'] = '';
    	
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
        var_dump($this->metadata);
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
    		$value = $string = preg_replace('/[\s\|]+/', '', $value);
    		$this->metadata[$name] = $value;
    		if ($name === 'iagaCode') {
    			$this->identifier = strtolower($value);
    		}
    	}
    }
    
    /**
     * Add line description from a line
     * @param string $line
     */
    private function addLineDescription($line) {
    	$this->metadata['description'] .= preg_replace('/\|+/', '', $line);
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
    		$fields[0] = 'DATETIME';
    		$this->isDatetime = true;
    	}
    	$this->fields = $fields;
    }
    
    /**
     * add data from a line
     * @param string $line
     */
    private function extractData($line) {
    	
    }
    
}