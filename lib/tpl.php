<?php

/**
 * Tpl
 *
 * Super simple template engine
 *
 * @package   Kirby Toolkit
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Tpl extends Silo {

  static public $data = array();
  static public $jade = null;

  static public function load($file, $data = array(), $return = true) {
    if(file_exists($file.'.jade')) {
		$content = static::_jade($file, true);
    	ob_start();
    	extract(array_merge(static::$data, (array)$data));
		require($content);
		$content = ob_get_contents();
		ob_end_clean();
    } elseif(file_exists($file)) {
    	ob_start();
		extract(array_merge(static::$data, (array)$data));
		require($file);
		$content = ob_get_contents();
		ob_end_clean();
	} else {
	    return false;
    }
    if($return) return $content;
    echo $content;
  }
  static private function _jade($fn, $file = false, $deps = array()) {
  	$jn = $fn.'.jade';
    $time = @filectime($jn);
    foreach($deps as $dn) {
        $x = @filectime($dn);
        if($x === FALSE)
            break;
        if($x > $time)
            $time = $x;
    }
    if($time === FALSE)
        die("can't open jade file '$fn'");

    if(!isset(static::$jade) || !static::$jade)
        static::$jade = new Jade\Jade(true, true);

    if($file) {
        $to = @filectime($fn);
        if($to === FALSE || $to < $time)
            file_put_contents($fn, static::$jade->render($jn));
        return $fn;
    }
    return $jade->render($jn);
	}

}