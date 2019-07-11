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

/**
 * Convert kp value like 0+, 1- in float value
 * @param string $kpvalue
 * @return number
 */
function kp2value($kpvalue) {
		if(gettype($kpvalue) != 'string'){
			return 0;
		}
		$num = floatval( $kpvalue[0]);
		
		switch($kpvalue[1]){
			case "+":
				$num += 0.333;
				break;
			case "-":
				$num -= 0.333;
				break;
		}
		
		return $num;
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
       // $this->initSeries();
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
        $this->colors[0] = Config::$styles[$this->code]['color'];
        $this->colors[1] = shadeColor($this->colors[0], -0.2);
        $this->colors[2] = shadeColor($this->colors[0], 0.1);
    }

    /**
     * Prepare data and optins for Highchart
     * @return array all the options to build highchart chart
     */
    private function getOptions() {
    	$this->initSeries();
        return array(
            'chart' => array(
                'height'             => 400, 
                'width'             => 800, 
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
                   // 'millisecond'=> '%H:%M:%S.%L',
                   // 'second'     =>'%H:%M:%S',
                  //  'minute'     =>'%H:%M',
                    'hour'       =>'%H:%M',
                    'day'        =>'%e %b %Y',
                    'week'       =>'%e. %b',
                    'month'      =>'%b %y',
                    'year'       =>'%Y'
                ),
                'crosshair'        => true
            ),
        	'plotOptions' => $this->getPlotOptions(),
            'yAxis'    => $this->getYAxis(),
            'tooltip' => $this->getTooltip(),
            'series'  => $this->getSeries()    
        );
    }
    private function getPlotOptions () {
    	$plotOptions = array( 'series' => array(
            'stacking' => "normal",
            'pointPadding' =>  0,
            'groupPadding' =>  0,
            'borderWidth' =>  1,
    	));
    	if (in_array($this->code, ['SFE', 'SC'])) {
    		$plotOptions['column'] = array('pointWidth' => 4);
    	}
    	return $plotOptions;
    }
    private function getTooltip () {
    	$tooltip = array(
    			'shared' => true,
    			'crosshaires' => [true, false, false]
    	);
    	
    	return $tooltip;
    }
    private function getYAxis() {
    	$yAxis = array();
    	$infos = Config::$styles[$this->code];
    	
    	switch($this->code) {
    		case 'aa':
    		case 'am':
    		case 'Kp':
    		case 'Dst':
    			if (!is_null($this->kp)) {
    				array_push($yAxis, array(
    						'title' => array(
    								'text' => $this->kp 
    						),
    						'minorTickInterval' => 3,
    						'tickLength' => 10,
    						'max' => 9,
    						'opposite' => true 
    				));
    			}
    			$html = '<span style="color:'. $this->colors[0] .'">';
    			$html .= '<b>'. $infos['name']. '</b></span> (' . $infos['unit']. ')';
    			array_unshift($yAxis, array(
    				'title' => array(
    				    'margin' => 45,
    					'useHTML' => true,
    					'text' => $html
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
    private function initSeriesDefaultIndice () {
         $data = array();
         $datakp = array();
         if (!is_null($this->kp)){
         	$searchkp = array_filter($this->fields, function ($value) {
         		if (preg_match('/^kp[am]{0,1}/i', $value)) {
         			return true;
         		} else {
         			return false;
         		}
         	});
         	$indexkp = array_keys($searchkp)[0];
         }
         $index = array_search($this->code, $this->fields, true);
         foreach($this->data as $values) {
         	 $date = new \DateTime($values[0]);
         	 $time = strtotime($values[0]);
         	
             // values of aa, am or kp  are in column $index
             if (!empty($values[$index]) && $values[$index] != '9999' && $values[$index] != '999.00') {
             	$point = array($values[0], $values[$index]);
                array_push($data, $point);
                if (!is_null($this->kp) && isset($values[$indexkp])) {
                	$point = array($values[0], kp2value($values[$indexkp]));
                	array_push($datakp, $point);
                }
             }
         }
         $this->series[0] = $data;
         $this->series[1] = $datakp;
         var_dump($this->series[0]);
    }
    private function initSeriesStations() {
        
    }
    private function getSeries() {
        $series = array();
        switch($this->code) {
            case 'aa':
            case 'Kp':
            case 'am':
            case 'Dst':
                if (!is_null($this->kp)) {
                    array_push($series, array(
                        'type' => 'column',
                        'name' => $this->kp,
                        'color' => $this->colors[2],
                        'yAxis' => 1,
                        'zIndex' => 1,
                        'data' => $this->series[1]
                    ));
                }
            default:
                array_unshift($series, array(
                        'type' => 'line',
                        'name' => $this->code,
                        'color'=> $this->colors[1],
                        'zIndex' => 2,
                        'data'  => $this->series[0]
                ));
                
        }
        return $series;
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