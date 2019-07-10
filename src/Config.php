<?php
namespace Iaga;

class Config {
    public static $styles = array(
        "aa" =>
              array (
              "name"   => "aa", 
              "unit"   => "nT", 
              "color"  => "#336699",
              "url"    => "http =>//isgi.unistra.fr/indices_aa.php",
              "series" => array( 
                  "aa" => array(),
                  "kp" => array()
              )
         ),
        "am" => array(
              "name"   => "am", 
              "unit"   => "nT", 
              "color"  => "#cc33cc", 
              "url"    => "http =>//isgi.unistra.fr/indices_am.php"
         ),
        "kp" => array(
              "name"   => "Kp", 
              "unit"   => "2nT", 
              "color"  => "#339933", 
              "url"    => "http =>//isgi.unistra.fr/indices_kp.php"
         ),
        "dst" => array(
              "name"   => "Dst", 
              "unit"   => "nT", 
              "color"  => "#ff6633", 
              "url"    => "http =>//isgi.unistra.fr/indices_dst.php"
         ),
        "pc" => array(
              "name"   => "PC", 
              "unit"   => "mV/m", 
              "color"  => "#3366ff", 
              "url"    => "http =>//isgi.unistra.fr/indices_pc.php"
         ),    
        "ae" => array(
              "name"   => "AE", 
              "unit"   => "nT", 
              "color"  => "#cc3366", 
              "url"    => "http =>//isgi.unistra.fr/indices_ae.php"
         ),
        "ck-days" => array(
              "name"   => "CK-days", 
              "unit"   => "", 
              "color"  => "#32CD32", 
              "url"    => "http =>//isgi.unistra.fr/events_ckdays.php"
         ),
        "q-days" => array(
              "name"   => "Q-days", 
              "unit"   => "", 
              "color"  => "#ff0000", 
              "url"    => "http =>//isgi.unistra.fr/events_qdays.php"
         ),
        "sc" => array(
              "name"   => "SC", 
              "unit"   => "", 
              "color"  => "#669933" , 
              "url"    => "http =>//isgi.unistra.fr/events_sc.php"
         ),
        "sfe" => array(
              "name"   => "SFE", 
              "unit"   => "", 
              "color"  => "#FF8000" , 
              "url"    => "http =>//isgi.unistra.fr/events_sfe.php"
         ),
        "asigma" => array(
              "name"   => "a&sigma;", 
              "unit"   => "nT", 
              "color"  => "", 
              "url"    => "http =>//isgi.unistra.fr/indices_asigma.php"
        )
  );
    
    public static function getStyles($code) {
    	
    }
    public static function kp2Value ($kp) {
        $num = floatval($kp[0]);
        switch($kp[1]) {
            case '+':
                $num += 0.333;
                break;
            case '-':
                $num += 0.333;
                break;
            default:
        }
    }
}