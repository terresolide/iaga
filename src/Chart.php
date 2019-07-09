<?php
namespace iaga;

include_once 'Iaga.php';

Class Chart extends Iaga{
	private $kp = null;
    
    public function __construct($iaga) {
        var_dump(gettype($iaga));
        switch(gettype($iaga)) {
            case 'string':
                parent::__construct($iaga);
                break;
            case 'object':
                foreach($this as $key => $value) {
                    $this->{$key} = $iaga->{$key};
                }
                break;
        }
        $this->getKpName();
        var_dump($this->kp);
    }
    
    /**
     * return an array ready to build graph with style
     * @return array
     */
    public function toChartArray() {
        
        var_dump(Config::$styles);
        if (!is_null($this->error)) {
            $rep = array("error" => $this->error);
        } else {
            include_once 'Config.php';
            // @todo
        }
        return $rep;
    }
    
    /**
     * return a json ready to build graph
     * @return string json
     */
    public function toChartJson() {
        $rep = $this->toChartArray();
        return json_encode($rep, JSON_NUMERIC_CHECK);
    }
    
    /**
     * return a html page with the graph
     * @return string HTML
     */
    public function toHTML($title) {
        $error = "Une erreur existe";
        $options = $this->getOptions();
        ob_start();
        include 'chart.phtml';
        return ob_get_clean();
    }
    
    private function getOptions() {
        return array(
            'chart' => array(
                'height'             => 200, 
                'width'             => 400, 
                'defaultSeriesType' => 'areaspline',
                'plotBorderColor'    => '#666666',
                'plotBorderWidth'     => 1
            ),
            'title' => array( 
                'text'         => '',
                'align'        => 'float'
            ),
            'xAxis'    => array(
                'type'         => 'datetime',
                'lineColor'    => '#666',
                'tickLength'=> 5,
                'dateTimeLabelFormats' => array(
                    'millisecond'=> '%H:%M:%S.%L',
                    'second'     =>'%H:%M:%S',
                    'minute'     =>'%H:%M',
                    'hour'         =>'%H:%M',
                    'day'         =>'%e %b %Y',
                    'week'         =>'%e. %b',
                    'month'     =>'%b %y',
                    'year'         =>'%Y'
                ),
                'crosshair'        => true
            ),
            'yAxis'    => $this->getYAxis(),
           // 'tooltip' => $this->getTooltip(),
            'series'  => $this->getSeries()    
        );
    }
    private function getYAxis() {
        return array();
    }
    private function getSeries() {
        $series = array();
        switch($this->code) {
            case 'aa':
            case 'kp':
            case 'am':
            	if (!is_null($this->kp)) {
	                array_push($series, array());
            	}
            default:
                array_unshift($series, array(
                ));
                
        }
    }
    private function getKpSerie() {
        // Add 1h30 for all dates
        
    }
    private function getKpName() {
    	foreach($this->fields as $key){
    		if( preg_match('/^Kp[a-z]?$/', $key)){
    			$this->kp = $key;
    			return $key;
    		}
    	}
    	return null;
    }
}