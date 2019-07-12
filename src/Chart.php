<?php
namespace Iaga;

// case: don't use vendor autoloader
include_once 'Dataset.php';
include_once 'Config.php';
include_once 'Chart/AaParameters.php';

Class Chart extends Dataset{
    /**
     * @var array Highchart options
     */
    private $options = array(
        'width' => 800,
        'height' => 300,
        'defaultSeriesType' => 'areaspline',
        'plotBorderColor'    => '#666666',
        'plotBorderWidth'     => 1
    );
    
    /**
     * Array of series parameters, ready for Highchart
     * @var array 
     */
    private $parameters = null;
    
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
        $this->setOptions($options);
        $this->initParameters();
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
    
    



    /**
     * Prepare data and options for Highchart
     * @return array all the options to build highchart chart
     */
    private function getOptions() {
        return array(
            'chart' => $this->options,
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
                $this->parameters = $parameters = new Chart\AaParameters($this->code, $this->data, $this->fields);
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