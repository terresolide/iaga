<?php
namespace Iaga;

// case: don't use vendor autoloader
include_once 'Dataset.php';
include_once 'Config.php';
include_once 'Chart/AaParameters.php';


Class Chart extends Dataset{
    /**
     * Some chart have kp indice whose treatment is different (for example they are displayed in column)
     * the kp is evaluated in constructor
     * @var string like 'Kpa' or other, null if have not kp
     */
 //   private $kp = null;
    
    /**
     * Array of colors according to code
     * @var Array
     */
 //   private $colors = array();
    
    /**
     * Array of parameters, ready for Highchart
     * @param array 
     */
    private $parameters = null;
    
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
       // $this->getKpName();
      //  $this->initColors();
       // $this->initSeries();
    }
    
    /**
     * return an array ready to build graph with style
     * @return array
     */
    public function toChartArray() {
    	if(count($this->series) === 0) {
    		$this->initSeries();
    	}
    	var_dump($this->series);
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
     * set graph color
     * @var string color in hexa
     */
    public function setColor($color) {
        $this->colors[0] = $color;
        $this->colors[1] = shadeColor($color, -0.2);
        $this->colors[2] = shadeColor($color, 0.3);
    }
    /**
     * Compute the appropriate colors according to indice or station
     */
    private function initColors() {
        $color =  Config::$styles[\strtolower($this->code)]['color'];
        $this->setColor($color);
    }

    /**
     * Prepare data and options for Highchart
     * @return array all the options to build highchart chart
     */
    private function getOptions() {
    	var_dump($this->code);
    	$parameters = new Chart\AaParameters($this->code, $this->data, $this->fields);
       // $parameters->initSeries();
    	var_dump($parameters->getYAxis());
        return array(
            'chart' => array(
                'height'             => 300, 
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
            'plotOptions' => $parameters->getPlotOptions(),
            'yAxis'    => $parameters->getYAxis(),
            'tooltip' => $parameters->getTooltip(),
            'series'  => $parameters->getSeries()    
        );
    }
    
    /**
     * prepare the plot Options for highchart according the indice
     * @return array 
     */
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
        switch($this->code) {
        	case 'aa':
        	case 'am':
        	case 'Kp':
        		$tooltip['formatter'] = 'formatterDefault';
        		break;
        	case 'Dst':
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
        		break;
        		
        }
        
        return $tooltip;
    }
    private function getYAxis() {
        $yAxis = array();
        $infos = Config::$styles[$this->code];
        // trouble with Kp indice whose denominations principe is different from indices aa and am: 
        // - the name of geomagnetic index is Kp 
        // - the linear values are ap
        
        
        switch($this->code) {
            case 'aa':
            case 'am':
            case 'Kp':
            case 'Dst':
            	$code = $this->code === 'Kp' ? 'ap' : $this->code;
                if (!is_null($this->kp)) {
                    $html = '<span style="color:'. $this->colors[2] .';font-weight:600;">';
                    $html .= $this->kp .'</span>';
                    array_push($yAxis, array(
                        'title' => array(
                            'useHTML' => true,
                            'text' => $html
                        ),
                    	'tickAmount' => 4,
                        'max' => 9,
                        'opposite' => true 
                    ));
                }
                $html = '<span style="color:'. $this->colors[1] .'">';
                $html .= '<b>'. $code. '</b></span> (' . $infos['unit']. ')';
                array_unshift($yAxis, array(
                    'title' => array(
                        'margin' => 20,
                        'useHTML' => true,
                        'text' => $html
                    ),
                	'tickAmount' => 4
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
    /**
     * Create one (or two) temporal series from data array
     * Used by Kp, aa, am and Dst indices
     * For the 'kp' indices add column kp
     */
    private function initSeriesDefaultIndice () {
    	 $code = ($this->code === 'Kp') ? 'ap' : $this->code;
         $data = array(); // line
         $datakp = array(); // column
         if (!is_null($this->kp)){
         	//search the index in each $this->data line of 'kp' (kpa, kpm or kp)
             $searchkp = array_filter($this->fields, function ($value) {
                 if (preg_match('/^kp[am]{0,1}/i', $value)) {
                     return true;
                 } else {
                     return false;
                 }
             });
             // the index of kp value in each line of $this->data
             $indexkp = array_keys($searchkp)[0];
         }
         // search the index of indice in each line of $this->data
         $index = array_search($code, $this->fields, true);
         var_dump($code);
         $i = 0;
         foreach($this->data as $values) {
             $date = new \DateTime( $values[0]);
             $microtime =  1000 * $date->format('U');
             if (!is_null($this->kp)) {
             	// add 1h30 (why?)
             	$microtime += 5400000;
             }
             // values of aa, am or kp  are in column $index
             if (!empty($values[$index]) && $values[$index] != '9999' && $values[$index] != '999.00') {
                 $point = array($microtime, $values[$index]);
                array_push($data, $point);
                if (!is_null($this->kp) && isset($values[$indexkp])) {
                    $point = array($microtime, kp2value($values[$indexkp]));
                    array_push($datakp, $point);
                }
             }
         }
         $this->series[0] = $data;
         $this->series[1] = $datakp;
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
    
}