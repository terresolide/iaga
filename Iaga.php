<?php

/**
 * IAGA
 * @license GNU
 * 
 **/

class Iaga {
	/** 
	 * @var string Geomagnetic code (uppercase) of indice or station like AA or KOU
	 */
	protected $identifier;
	/**
	 * @var array associative containing timeResolution, title, bbox, temporalExtent, processingLevel ...
	 */
	private $metadata;
	/**
	 * @var array 
	 */
	private $data;
	
	public function __construct() {
		$this->metadata = array();
		$this->data = array();
	}
	
	/**
	 * Fill metadata and data from Iaga path or url
     * @param string $filename path or url to Iaga file
	 */
	public function loadFile ($filename) {
		//@todo
	}
	/**
	 * Fill metadata and data from Iaga ftp 
	 * @todo
	 */
	public function loadFromFtp($ftp) {
		
	}
	/**
	 * Merge data from another file
	 * @param \Iaga $iaga 
	 * @param boolean $force merge data even if they have different processingLevel
	 */
	public function merge($iaga, $force=false) {
		// check if it's the same code
		
		// check if dates are contiguous
		
		// check if they have same processing Level
		
		// merge in order the data 
		
	}
	/**
	 * get metadata
	 * @return Array the array of metadata
	 */
	public function getMetadata() {
		return $this->metadata;
	}
	/**
	 * get data
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}
	public function toJson() {
		$rep = array(
				"metadata" => $this->metadata,
				"data"     => $this->data
		);
		return json_encode($rep, JSON_NUMERIC_CHECK);
	}
	public function toXml() {
		// @todo
	}
	public function toJsonGraph() {
		$rep = [];
		// @todo
		return json_encode($rep, JSON_NUMERIC_CHECK);
	}
	
}