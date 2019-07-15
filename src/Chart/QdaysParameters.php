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
class QdaysParameters extends AbstractParameters{
    /** define the 2 colors for disturb and quiet days */
    protected $colors = array( 'Ddays' => '#DB1702', 'Qdays' => '#32CD32');
    
    public function getTooltip() {
        return   array(
                'shared' => true,
                'crosshaires' => [true, false, false],
                'formatter' => 'formatterQdays'
        );
    }
    public function getSeries() {
        $series = array();
        array_push($series, array(
            'name' => 'Ddays',
            'type' => 'column',
            'color'=> $this->colors['Ddays'],
            'stack'=> 'Days',
            'data' => $this->data['Ddays']
        ));

        array_push($series, array(
            'name' => 'Qdays',
            'type' => 'column',
            'color'=> $this->colors['Qdays'],
            'stack'=> 'Days',
            'data' => $this->data['Qdays']
        ));
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
        $html = '<span style="color:'. $this->colors['Ddays'] .'">';
        $html .= '<b>Ddays</b></span> / ';
        $html .= '<span style="color:'. $this->colors['Qdays'] .'"><b>Qdays</b></span>';
        array_unshift($yAxis, array(
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
    protected function initData($fields, $dataSrc) {

        $dataDdays = array(); 
        $dataQdays= array(); 

        //search the index in each $this->data line of 'days'
        $searchKeyDays = array_filter($fields, function ($value) {
            if (preg_match('/^day/i', $value)) {
                return true;
            } else {
                return false;
            }
        });
        // the index of days value in each line of $this->data
        $indexDays = array_keys($searchKeyDays)[0];

        for($i = $this->extent['min']; $i <= $this->extent['max']; $i++) {
            $date = new \DateTime( $dataSrc[$i][0]);
            $microtime =  1000 * $date->format('U');
            if ($dataSrc[$i][$indexDays][0] === 'D') {
                // if value begin by D then add to array disturb days
                array_push($dataDdays, array($microtime, 1));
            } else if ($dataSrc[$i][$indexDays][0] === 'Q') {
                // if value begin by Q then add to array quiet days
                array_push($dataQdays, array($microtime, 1));
            }
        }
        $this->data['Ddays'] = $dataDdays;
        $this->data['Qdays'] = $dataQdays;
    }

}