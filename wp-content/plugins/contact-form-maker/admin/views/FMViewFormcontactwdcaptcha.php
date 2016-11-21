<?php

class FMViewFormcontactwdcaptcha {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  private $model;


  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct($model) {
    $this->model = $model;
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function display() {
	
    if (isset($_GET['action']) && esc_html($_GET['action']) == 'formcontactwdcaptcha') {
      $i = (isset($_GET["i"]) ? esc_html($_GET["i"]) : '');
      $r2 = (isset($_GET["r2"]) ? (int) $_GET["r2"] : 0);
      $rrr = (isset($_GET["rrr"]) ? (int) $_GET["rrr"] : 0);
      $randNum = 0 + $r2 + $rrr;
      $digit = (isset($_GET["digit"]) ? (int) $_GET["digit"] : 6);
      $cap_width = $digit * 10 + 15;
      $cap_height = 26;
      $cap_quality = 100;
      $cap_length_min = $digit;
      $cap_length_max = $digit;
      $cap_digital = 1;
      $cap_latin_char = 1;
      function code_generic($_length, $_digital = 1, $_latin_char = 1) {
        $dig = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        $lat = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        $main = array();
        if ($_digital) {
          $main = array_merge($main, $dig);
        }
        if ($_latin_char) {
          $main = array_merge($main, $lat);
        }
        shuffle($main);
        $pass = substr(implode('', $main), 0, $_length);
        return $pass;
      }
      $l = rand($cap_length_min, $cap_length_max);
      $code = code_generic($l, $cap_digital, $cap_latin_char);
      if (session_id() == '' || (function_exists('session_status') && (session_status() == PHP_SESSION_NONE))) {
        @session_start();
      }

      $_SESSION[$i . '_wd_captcha_code'] = md5($code);
      $canvas = imagecreatetruecolor($cap_width, $cap_height);
      $c = imagecolorallocate($canvas, rand(150, 255), rand(150, 255), rand(150, 255));
      imagefilledrectangle($canvas, 0, 0, $cap_width, $cap_height, $c);
      $count = strlen($code);
      $color_text = imagecolorallocate($canvas, 0, 0, 0);
      for ($it = 0; $it < $count; $it++) {
        $letter = $code[$it];
        imagestring($canvas, 6, (10 * $it + 10), $cap_height / 4, $letter, $color_text);
      }
      for ($c = 0; $c < 150; $c++) {
        $x = rand(0, $cap_width - 1);
        $y = rand(0, 29);
        $col = '0x' . rand(0, 9) . '0' . rand(0, 9) . '0' . rand(0, 9) . '0';
        imagesetpixel($canvas, $x, $y, $col);
      }
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      header('Cache-Control: no-store, no-cache, must-revalidate');
      header('Cache-Control: post-check=0, pre-check=0', FALSE);
      header('Pragma: no-cache');
      header('Content-Type: image/jpeg');
      imagejpeg($canvas, NULL, $cap_quality);
    }
    die('');
  }

  ////////////////////////////////////////////////////////////////////////////////////////
  // Getters & Setters                                                                  //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Private Methods                                                                    //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Listeners                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
}