<?php
namespace Iaga\Chart;

// in case there is no vendor autoloader
require_once 'AbstractParameters.php';

/**
 * Compute Chart Parameters for Highchart reserve to indice
 * Qdays
 * @author epointal
 *
 */
class CKdaysParameters extends AbstractParameters{
    /** list of CK possible values */
    private $ckValues = array('CC', 'KC', 'CK', 'KK', 'C-', 'K-' , '-C', '-K');
    
    public function __construct($code, $data, $fields, $extent, $temporalExtent) {
        $this->setColor(\Iaga\Config::$styles[$code]['color']);
        parent::__construct($code, $data, $fields, $extent, $temporalExtent);
    }
    public function getTooltip() {
        return   array(
                'shared' => true,
                'crosshaires' => [true, false, false],
                'formatter' => 'formatterQdays'
        );
    }
    public function getSeries() {
        $series = array();
        foreach($this->data as $name => $serie) {
            if (count($serie) > 0) {
                array_push($series, array(
                    'name' => $name,
                    'type' => 'column',
                    'color'=> $this->colors[$name],
                    'stack'=> $name,
                    'data' => $serie
                ));
            }
        }
        if (count($this->hidden)) {
            array_push($series, array(
                'name' => 'hidden',
                'type' => 'area',
                'color' => 'rgba(255, 255, 255, 0.1)',
                'data' => $this->hidden
            ));
        }
        return $series;
    }
    public function getYAxis() {
        $yAxis = array();
        $html = '<span style="color:'. $this->colors['CC'] .'">';
        $html .= '<b>CK24</b></span> / ';
        $html .= '<span style="color:'. $this->colors['-C'] .'"><b>CK48</b></span>';
        array_push($yAxis, array(
                'title' => array(
                        'margin' => 20,
                        'useHTML' => true,
                        'text' => $html
                ),
        		'tickAmount' => 1,
                'min'=> 0,
                'max' => 1
        ));
        return $yAxis;
    }
    
    public function setColor ($color) {
        foreach($this->ckValues as $i => $ck) {
            $this->colors[$ck] = \Iaga\shadeColor($color, 0.08 * ($i - 1) );
        }
    }
    protected function initData($fields, $dataSrc) {
        // create a serie by value if CK24+CK48
        foreach($this->ckValues as $key) {
            $this->data[$key] = array();
        }

        //search the index in each $this->data line of 'CK24' and 'CK48
        $searchKeyCK = array_filter($fields, function ($value) {
            if (preg_match('/^ck/i', $value)) {
                return true;
            } else {
                return false;
            }
        });
        // the index of days value in each line of $this->data
        $indexCK24 = array_keys($searchKeyCK)[0];
        $indexCK48 = array_keys($searchKeyCK)[1];
        for($i = $this->extent['min']; $i <= $this->extent['max']; $i++) {
            $date = new \DateTime( $dataSrc[$i][0]);
            $microtime =  1000 * $date->format('U');
            $value = $dataSrc[$i][$indexCK24].$dataSrc[$i][$indexCK48];
            array_push($this->data[$value], array($microtime, 1));
        }
    }

}