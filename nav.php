<?php
$menu = array(
		'home'  => array('text'=>'Home',  'url'=>'index.php'),
		'Servers'  => array('text'=>'Servers',  'url'=>'Servers.php'),
		'Patches' => array('text'=>'Patches', 'url'=>'Packages.php'),
);

class CNavigation {
  public static function GenerateMenu($items) {
    $html = "<nav>\n";
    foreach($items as $item) {
      $html .= "<a href='{$item['url']}'>{$item['text']}</a>\n";
    }
    $html .= "</nav>\n";
    return $html;
  }
};
	
?>