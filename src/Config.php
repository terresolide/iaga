<?php
namespace Iaga;

/**
 * lightens or darkens a color (darkens when percent < 0)
 * @param string $hex color in hexadecimal where length = 7 (not accepted color like #aaa)
 * @param float $percent number between 0 and 1
 * @return string an hexadecimal color
 */
function shadeColor($hex, $percent) {
	$rgb = str_split(trim($hex, '# '), 2);
	
	foreach ($rgb as &$hex) {
		$color = hexdec($hex);
		$adjustableLimit = $percent < 0 ? $color : 255 - $color;
		$adjustAmount = ceil($adjustableLimit * $percent);
		$hex = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
	}
	return '#'.implode($rgb);
}

/**
 * Convert kp value like 0+, 1- in float value
 * @param string $kpvalue
 * @return number
 */
function kp2value($kpvalue) {
	if(gettype($kpvalue) != 'string'){
		return 0;
	}
	$num = floatval( $kpvalue[0]);
	
	switch($kpvalue[1]){
		case "+":
			$num += 0.333;
			break;
		case "-":
			$num -= 0.333;
			break;
	}
	
	return $num;
}

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