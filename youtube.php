<?php
include('simple_html_dom.php');
$url = str_ireplace("https", "http", $_GET['path']);
$dom = file_get_html($url);
// echo htmlspecialchars($dom->innertext);
$scripts = $dom->find('div[id=player]',0)->find('script');
$searchText = "";
foreach ($scripts as $script) {
	$searchText .= $script->innertext;
}
// echo urldecode($searchText);
$pattern = '/ytplayer.config = {.*};/';
preg_match($pattern, $searchText, $matches);
$json = substr(str_ireplace("ytplayer.config = ", "", $matches[0]),0,-1);
$params = json_decode($json,1);
/*echo "<pre>";
print_r($params);
echo "</pre>";*/

$title = $params['args']['title'];
$allLinks = explode(",", $params['args']['url_encoded_fmt_stream_map']); // brings flv
// $allLinks = explode(",", $params['args']['adaptive_fmts']); // video/audio separate

echo "<h3>" . urldecode($title) . "</h3>";
foreach ($allLinks as $key => $value) {
	// echo "<h1>$key</h1>" . urldecode($value) . "<br /><br />";
	$each = explode("&",$value);
	// sort($each,SORT_STRING);
	$url = "";
	$sig = "";
	$quality = "";
	$type = "";
	foreach ($each as $val) {
		$param = explode("=", $val);

		if ($param[0]=="url")
			$url = substr(urldecode($val), 4);
		else if($param[0]=="sig")
			$sig = $param[1];
		else if($param[0]=="quality")
			$quality = urldecode($param[1]);
		else if($param[0]=="type")
			$type = urldecode($param[1]);
		// echo urldecode($val) . "<br />";
	}
	$downloadURL = $url . "&signature=" . $sig . "&title=" . urlencode($title);
	echo "<strong>$quality : $type</strong><br />";
	echo "<b>Download:</b> <a href=\"" . $downloadURL . "\">Link</a>";
	echo "<br /><br />";
}
/*$pieces = explode("&", urldecode($searchText));
echo "<pre>";
foreach ($pieces as $key => $value) {
	echo htmlspecialchars(urldecode($value)) . "\n";
}
echo "</pre>";*/
?>