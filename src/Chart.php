<?php
namespace Iaga;

// case: don't use vendor autoloader
include_once 'Dataset.php';
include_once 'Config.php';

/**
 * lightens or darkens a color (darkens when percent < 0)
 * @param string $hex color in hexadecimal where length = 7 (not accepted color like #aaa)
 * @param float $percent number between 0 and 1
 * @return string an hexadecimal color
 */
function shadeColor($hex, $percent) {
    $rgb = str_split(trim($hex, '# '), 2);
    
    foreach ($rgb as &$hex) {
        $color = hexdec($hex);
        $adjustableLimit = $percent < 0 ? $color : 255 - $color;
        $adjustAmount = ceil($adjustableLimit * $percent);
        $hex = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
    }
    return '#'.implode($rgb);
}

Class Chart extends Dataset{
    /**
     * Some chart have kp indice whose treatment is different (for example they are displayed in column)
     * the kp is evaluated in constructor
     * @var string like 'Kpa' or other, null if have not kp
     */
    private $kp = null;
    
    /**
     * Array of colors according to code
     * @var Array
     */
    private $colors = array();
    
    /**
     * Array of data, ready for Highchart
     * @param array 
     */
    private $series = array();
    
    public function __construct($iaga) {
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
        $this->initColors();
        $this->initSeries();
    }
    
    /**
     * return an array ready to build graph with style
     * @return array
     */
    public function toChartArray() {
        if (!is_null($this->error)) {
            $rep = array("error" => $this->error);
        } else {
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
      //  $error = "Une erreur existe";
        $options = $this->getOptions();
        ob_start();
        include 'chart.phtml';
        return ob_get_clean();
    }
    
    /**
     * Compute the appropriate colors according to indice or station
     */
    private function initColors() {
        
    }

    /**
     * Prepare data and optins for Highchart
     * @return array all the options to build highchart chart
     */
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
                    'hour'       =>'%H:%M',
                    'day'        =>'%e %b %Y',
                    'week'       =>'%e. %b',
                    'month'      =>'%b %y',
                    'year'       =>'%Y'
                ),
                'crosshair'        => true
            ),
            'yAxis'    => $this->getYAxis(),
           // 'tooltip' => $this->getTooltip(),
            'series'  => $this->getSeries()    
        );
    }
    private function getYAxis() {
    	$yAxis = array();
    	$infos = Config::$styles[$this->code];
    	switch($this->code) {
    		case 'aa':
    		case 'am':
    		case 'Kp':
    		case 'Dst':
    			array_unshift($yAxis, array(
    				'title' => array(
    				    'margin' => 45,
    					'useHTML' => true,
    					'text' => '<span style="color:'. $infos['color'] .'">' .
    					'<b>'. $infos['name']+'</b></span> (' . $infos['unit']. ')'
    				)
    			));
    			break;
    		case 'PC':
    			break;
    		case 'SC':
    		case 'SFE':
    			break;
    		case 'CK-days':
    			break;
    		case 'Q-days':
    			break;
    		case 'asigma':
    		case 'AE':
    			break;
    			// station
    		default:
    			$this->initSeriesStations();
    			break;
    	}
        return $yAxis;
    }
    
    /**
     * Compute the array of data, ready for highchart
     */
    private function initSeries() {
        switch ($this->code) {
            case 'aa':
            case 'am':
            case 'Kp':
                $this->initSeriesKp();
            case 'Dst':
                $this->initSeriesDefaultIndice();
                break;
            case 'PC':
                break;
            case 'SC':
            case 'SFE':
                break;
            case 'CK-days':
                break;
            case 'Q-days':
                break;
            case 'asigma':
            case 'AE':
                break;
            // station
            default:
                $this->initSeriesStations();
                break;
                
        }
        
    }
    private function initSeriesKp() {
        
    }
    private function initSeriesDefaultIndice () {
         $data = array();
         $index = array_search($this->code, $this->fields, true);
         foreach($this->data as $values) {
             // values of aa, am or kp  are in column $index
             if (!empty($values[$index]) && $values[$index] != '9999' && $values[$index] != '999.00') {
                 $point = array($values[0], $values[$index]);
                 array_push($data, $point);
             }
         }
         $this->series[0] = $data;
    }
    private function initSeriesStations() {
        
    }
    private function getSeries() {
        $series = array();
        switch($this->code) {
            case 'aa':
            case 'kp':
            case 'am':
//                 if (!is_null($this->kp)) {
//                     array_push($series, array(
//                         'type' => 'column',
//                         'name' => $this->kp,
//                         'color' => $this->colors[2],
//                         'yAxis' => 1,
//                         'zIndex' => 1,
//                         'data' => $this->series[1]
//                     ));
//                 }
            default:
                array_unshift($series, array(
                        'type' => 'line',
                        'name' => $this->code,
                        'color'=> Config::$styles[$this->code]['color'],
                        'zIndex' => 2,
                        'data'  => $this->series[0]
                ));
                
        }
        return $series;
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