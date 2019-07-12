<?php
/**
 * 
 */
namespace Iaga\Chart;

// case don't use vendor autoloader
include_once realpath(__DIR__.'/../Config.php');

abstract class AbstractParameters{
    protected $code;
    protected $color;
    protected $series;

    public function __construct($code, $data, $fields) {
        $this->code = $code;
        $this->fields = $fields;
        $this->initColors(\Iaga\Config::$styles[strtolower($this->code)]);
        $this->initSeries($fields, $data);
    }

    abstract public function getSeries();
    abstract public function getTooltip();
    abstract public function getYAxis();
    
    /**
     * prepare the plot Options for highchart according to the indice
     * @return array
     */
    public function getPlotOptions () {
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
    
    public function setColor($color) {
        $this->colors[0] = $color;
        $this->colors[1] = \Iaga\shadeColor($color, -0.2);
        $this->colors[2] = \Iaga\shadeColor($color, 0.3);
    }
    
    protected function initColors() {
        $color =  \Iaga\Config::$styles[\strtolower($this->code)]['color'];
        $this->setColor($color);
    }
    
    /**
     * @var array $fields field list as it was stored in file
     * @var array $data the file data as it was stored
     * Compute the array of data, ready for highchart
     */
    abstract protected function initSeries($fields, $data);


}