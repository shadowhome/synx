<?php
ini_set('error_reporting', E_ALL);
$menu = array(
		'home'  => array('text'=>'Home',  'url'=>'index.php'),
		'Servers'  => array('text'=>'Servers',  'url'=>'servers.php'),
		'Patches' => array('text'=>'Packages', 'url'=>'packages.php'),
);

class CNavigation {
  public static function GenerateMenu($items) {
  	$html = ".";
    $html .= '<nav class="navbar navbar-inverse">';
      $html .= '<div class="container-fluid">';
        $html .= '<div class="navbar-header">';
          $html .= '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">';
            $html .= '<span class="sr-only">Toggle navigation</span>';
            $html .= '<span class="icon-bar"></span>';
            $html .= '<span class="icon-bar"></span>';
            $html .= '<span class="icon-bar"></span>';
          $html .= '</button>';
          $html .= '<a class="navbar-brand" href="#">Shadow Servers</a>';
        $html .= '</div>';

        $html .= '<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">';
          $html .= '<ul class="nav navbar-nav">';
              foreach($items as $item) {
                $url = $item["url"];
                $text = $item["text"];
                $html .= '<li><a href="'.$url.'">'.$text.'</a></li>';
              } 
          $html .= '</ul>';
        $html .= '</div>';
      $html .= '</div>';
    $html .= '</nav>';
    return $html;
  }
};

?>