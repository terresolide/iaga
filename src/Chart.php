<?php
namespace Iaga;

// in case there is no vendor autoloader
include_once 'Dataset.php';


Class Chart extends Dataset{
    /**
     * mostly used when data are limited by a temporal extend
     * by default min = 0 and max is last index of array data
     * @var array of indexmin and indexmax of used data
     */
    private $extend = array('min' =>null, 'max' =>null);
    
    /**
     * @var array Highchart chart options
     */
    private $options = array(
        'width' => 800,
        'height' => 300,
        'defaultSeriesType' => 'areaspline',
        'plotBorderColor'   => '#666666',
        'plotBorderWidth'   => 1
    );
    
    /**
     * Array of series parameters, ready for Highchart
     * @var array 
     */
    private $parameters = null;
    
    /**
     * datemin and datemax to limit the data displayed or extend the chart
     * @var array $temporalExtend 
     */
    private $temporalExtend = array('min' =>null, 'max' =>null);
    
    /**
     * @var string $title
     */
    private $title = '';
    
    /**
     * Constructor
     * @param \Iaga\Dataset $iaga
     * @param array $options
     */
    public function __construct($iaga, $options = array()) {
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
        $this->computeExtend();
        $this->setOptions($options);
    }
    
    /**
     * @param string $datemin
     * @param string $datemax
     */
    public function addTemporalExtend($datemin, $datemax = null) {
        $this->parameters = null;
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

        $this->temporalExtend = array('min' => $date0, 'max' => $date1);
        $this->computeExtend($date0, $date1);
    }
    
    public function removeTemporalExtend() {
        $this->temporalExtend = array('min' => null, 'max'=> null);
        $this->extend = array('min' => 0, 'max' => count($this->data) - 1);
        $this->parameters = null;
    }
    
    /**
     * set graph color
     * @var string|array color in hexa or array of colors
     */
    public function setColor($color) {
        $this->parameters->setColor($color);
    }
    /**
     * set graph height
     * @var int $height
     */
    public function setHeight($height) {
        $this->options['height'] = $height;
    }
    
    /**
     * set chart options
     * @var array $options
     */
    public function setOptions($options) {
        $this->options = array_merge($this->options, $options);
    }
    
    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }
    /**
     * set width
     * @var int $width
     */
    public function setWidth($width) {
        $this->options['width'] = $width;
    }
    
    /**
     * return an array ready to build graph with style
     * @return array
     */
    public function toChartArray() {
    	if(count($this->series) === 0) {
    		$this->initSeries();
    	}
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
     * @var string $title
     * @return string HTML
     */
    public function toHTML($title = '') {
      //  $error = "Une erreur existe";
        $options = $this->getOptions();
        ob_start();
        include 'chart.phtml';
        return ob_get_clean();
    }
    private function computeExtend() {
        $min = 0;
        $max = count($this->data) - 1;
       
        if (!is_null($this->temporalExtend['min'])) {
            // Search the first index of data where the date is upper than temporalExtend['min']
            while($this->data[$min][0] < $this->temporalExtend['min'] && $min < $max) {
                $min ++;
            }
        }
        if (!is_null($this->temporalExtend['max'])) {
            // Search the last index of data where the date is minor than temporalExtend['max']
            while($this->data[$max][0] > $this->temporalExtend['max'] && $min < $max) {
                $max--;
            }
        }
        $this->extend = array( 'min' => $min, 'max' => $max);
    }
    /**
     * Prepare data and options for Highchart
     * @return array all the options to build highchart chart
     */
    private function getOptions() {
        if (is_null($this->parameters)) {
            $this->initParameters();
        }
        return array(
            'chart' => $this->options,
            'title' => array( 
                'text'         => '',
                'align'        => 'float'
            ),
            'legend' => array(
                'enabled' => false
            ),
            'credits' => array(
                'enabled' => false
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
            'plotOptions' => $this->parameters->getPlotOptions(),
            'yAxis'    => $this->parameters->getYAxis(),
            'tooltip' => $this->parameters->getTooltip(),
            'series'  => $this->parameters->getSeries()    
        );
    }

    private function initParameters() {
        switch ($this->code) {
            case 'aa':
            case 'am':
            case 'Kp':
            case 'Dst':
                // in case there is no vendor autoloader
                include_once 'Chart/AaParameters.php';
                $this->parameters = new Chart\AaParameters(
                    $this->code,
                    $this->data,
                    $this->fields,
                    $this->extend,
                    $this->temporalExtend
                );
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
            // stations
            default:
                break;
        }
    }
}