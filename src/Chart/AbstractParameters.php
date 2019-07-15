<?php
/**
 * 
 */
namespace Iaga\Chart;

// case don't use vendor autoloader
include_once realpath(__DIR__ . '/../Config.php');

abstract class AbstractParameters{
    protected $code;
    protected $colors;
    
    /**
     * @var array $data reorganize for Highchart array of serie [microtime, value]
     */
    protected $data;

    /**
     * @var array $hidden serie of 2 dates to extent graph from a datemin to a datemax
     */
    protected $hidden = array();
    
    protected $extent = null;
    /**
     * @param string $code iaga code of indice or station
     * @param array $data the data from iaga file as if
     * @param array $fields list of fields in iaga file
     */
    public function __construct($code, $data, $fields, $extent, $temporalExtent) {
        date_default_timezone_set('UTC');
        $this->code = $code;
        $this->fields = $fields;
        $this->extent = $extent;
        $this->initHidden($temporalExtent);
        $this->initColors(\Iaga\Config::$styles[strtolower($this->code)]);
        $this->initData($fields, $data);
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
        $this->colors[1] = \Iaga\shadeColor($color, 0.5);
        $this->colors[2] = \Iaga\shadeColor($color, -0.2);
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
    abstract protected function initData($fields, $data);
    
    /**
     * Add a data of hidden data, the goal is to have chart from a datemin to a datemax
     * even if the real data is not in this bounds
     */
    protected function initHidden ($temporalExtent) {
        if ( !is_null($temporalExtent['min']))  {
            $date0 = new \DateTime($temporalExtent['min']);
            $microtime =  1000 * $date0->format('U');
            array_push($this->hidden, array($microtime, 1));
        }
        if (!is_null($temporalExtent['max'])) {
            $date1 = new \DateTime( $temporalExtent['max']);
            $microtime =  1000 * $date1->format('U');
            array_push($this->hidden, array($microtime, 1));
        }
    }


}