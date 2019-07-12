<?php
namespace Iaga\Chart;
abstract class AbstractParameters{
	protected $code;
	protected $color;
	// protected $data = array();
	protected $series;
	
	public function __construct($code, $data, $fields) {
		var_dump($code);
		$this->code = $code;
		$this->fields = $fields;
		$this->initColors(\Iaga\Config::$styles[strtolower($this->code)]);
		$this->initSeries($fields, $data);
	}
	abstract public function getYAxis();
	abstract protected function initSeries($fields, $data);
	abstract public function getTooltip();
	abstract public function getSeries();
	public function setColor($color) {
		$this->colors[0] = $color;
		$this->colors[1] = \Iaga\shadeColor($color, -0.2);
		$this->colors[2] = \Iaga\shadeColor($color, 0.3);
	}
	protected function initColors() {
		$color =  \Iaga\Config::$styles[\strtolower($this->code)]['color'];
		$this->setColor($color);
	}
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
	
}