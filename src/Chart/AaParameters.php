<?php
namespace Iaga\Chart;

require_once 'AbstractParameters.php';

/**
 * Compute Chart Parameters for Highchart reserve to indice
 * aa, am, Kp and Dst
 * @author epointal
 *
 */
class AaParameters extends AbstractParameters{
	
	private $kp = null;
	
	public function __construct($code, $data, $fields) {
		$this->getKpName($fields);
		parent::__construct($code, $data, $fields);
		var_dump($this->code);

	}
	public function getYAxis() {
		$yAxis = array();
		$infos = \Iaga\Config::$styles[strtolower($this->code)];
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
		return $yAxis;
	}
	protected function initSeries($fields, $dataSrc) {
		$code = ($this->code === 'Kp') ? 'ap' : $this->code;
		$data = array(); // line
		$datakp = array(); // column
		if (!is_null($this->kp)){
			//search the index in each $this->data line of 'kp' (kpa, kpm or kp)
			$searchkp = array_filter($fields, function ($value) {
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

		$i = 0;
		foreach($dataSrc as $values) {
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
					$point = array($microtime, \Iaga\kp2value($values[$indexkp]));
					array_push($datakp, $point);
				}
			}
		}
		$this->series[0] = $data;
		$this->series[1] = $datakp;	
	}
	public function getTooltip() {
		return   array(
				'shared' => true,
				'crosshaires' => [true, false, false],
				'formatter' => 'formaterDefault'
		);
	}
	public function getSeries() {
		$series = array();
		$name = ($this->code === 'Kp') ? 'ap' : $this->code;
		
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
	
		array_unshift($series, array(
			'type' => 'line',
			'name' => $name,
			'color'=> $this->colors[1],
			'zIndex' => 2,
			'data'  => $this->series[0]
		));
		return $series;
	}
	private function getKpName($fields) {
		foreach($fields as $key){
			if( preg_match('/^Kp[a-z]?$/', $key)){
				$this->kp = $key;
				return $key;
			}
		}
		return null;
	}
}