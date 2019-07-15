<?php
namespace Iaga\Chart;

// in case there is no vendor autoloader
require_once 'AbstractParameters.php';

/**
 * Compute Chart Parameters for Highchart reserve to indice
 * aa, am, Kp and Dst
 * @author epointal
 *
 */
class AaParameters extends AbstractParameters{
    
    private $kp = null;
    
    public function __construct($code, $data, $fields, $extent, $temporalExtent) {
        $this->getKpName($fields);
        parent::__construct($code, $data, $fields, $extent, $temporalExtent);
    }

    public function getTooltip() {
        return   array(
                'shared' => true,
                'crosshaires' => [true, false, false],
                'formatter' => 'formatterDefault'
        );
    }
    public function getSeries() {
        $series = array();
        $name = ($this->code === 'Kp') ? 'ap' : $this->code;
        
        if (!is_null($this->kp)) {
            array_push($series, array(
                'type' => 'column',
                'name' => $this->kp,
                'color' => $this->colors[1],
                'yAxis' => 1,
                'zIndex' => 1,
                'data' => $this->data[1]
            ));
        }
    
        array_unshift($series, array(
            'type' => 'line',
            'name' => $name,
            'color'=> $this->colors[0],
            'zIndex' => 2,
            'data'  => $this->data[0]
        ));
        if (count($this->hidden)) {
            array_push($series, array(
                'name' => 'hidden',
                'color' => 'rgba(255, 255, 255, 0.1)',
                'data' => $this->hidden
            ));
        }
        return $series;
    }
    public function getYAxis() {
        $yAxis = array();
        $infos = \Iaga\Config::$styles[strtolower($this->code)];
        $code = $this->code === 'Kp' ? 'ap' : $this->code;
        if (!is_null($this->kp)) {
            $html = '<span style="color:'. $this->colors[1] .';font-weight:600;">';
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
        $html = '<span style="color:'. $this->colors[0] .'">';
        $html .= '<b>'. $code. '</b></span> (' . $infos['unit']. ')';
        array_unshift($yAxis, array(
                'title' => array(
                        'margin' => 20,
                        'useHTML' => true,
                        'text' => $html
                ),
                'gridLineColor' => '#efefef'
        ));
        return $yAxis;
    }
    public function setSimple () {
        
    }
    protected function initData($fields, $dataSrc) {
        // Create one (or two) temporal series from data array
        // Used by Kp, aa, am and Dst indices
        // For the 'kp' indices add column kp
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
        for($i = $this->extent['min']; $i <= $this->extent['max']; $i++) {
            $date = new \DateTime( $dataSrc[$i][0]);
            $microtime =  1000 * $date->format('U');
            if (!is_null($this->kp)) {
                // add 1h30 (why?)
                $microtime += 5400000;
            }
            // values of aa, am or kp  are in column $index
            if (!empty($dataSrc[$i][$index]) && $dataSrc[$i][$index] != '9999' && $dataSrc[$i][$index] != '999.00') {
                $point = array($microtime, $dataSrc[$i][$index]);
                array_push($data, $point);
                if (!is_null($this->kp) && isset($dataSrc[$i][$indexkp])) {
                    $point = array($microtime, \Iaga\kp2value($dataSrc[$i][$indexkp]));
                    array_push($datakp, $point);
                }
            }
        }
        $this->data[0] = $data;
        $this->data[1] = $datakp;
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