
<?php require_once('simplehtmldom_1_5/simple_html_dom.php') ?>
<?php
  ini_set('max_execution_time', 300);
?>
<?php
	$base	= 'https://notjustok.com/category/download-mp3/';
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_URL, $base);
	curl_setopt($curl, CURLOPT_REFERER, $base);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$str = curl_exec($curl);
	curl_close($curl);

	$dom = new simple_html_dom();
    $dom->load($str);
    
  $musicLinks = [];
  $postString = 'div.card-content';
  $posts = $dom->find($postString);
  
  foreach($posts as $key=>$node){
    $text = $node->find('span a')[0]->innertext;
    $artistAndSong = trim(preg_replace('/^\s*\[(\w+)\]\s*/i',"", $text));

    // echo 'Artist & Song : '.$artistAndSong.'<br />';
    $text1 = str_replace('’', '',$text);
    $songLinks = str_replace(" ","-",trim(strtolower(preg_replace("/[\W|\d+]+/", " ", $text1))));

    $url = "https://notjustok.com/download-mp3/$songLinks";
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_REFERER, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $str = curl_exec($curl);
    curl_close($curl);

    $musicPage = new simple_html_dom();
    $musicPage->load($str);
    
    $song = $musicPage->find('a[rel="noopener"]', 0);

    if (isset($song->href) && strpos($song->href, 'notjustok.com') !== false) {
      echo 'Artist & Song : '.$artistAndSong.'<br />';
      echo 'Song Links : '.$song.'<br /><br />';
      array_push($musicLinks, $song->href);
    } 

    $musicPage->clear(); 
    unset($musicPage);
	}
	
	$dom->clear(); 
	unset($dom);
?>
<script
src="https://code.jquery.com/jquery-3.3.1.min.js"
integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
crossorigin="anonymous"></script>
<script>
  (function() {
    const data = <?php echo json_encode($musicLinks)?>;
    const links = data.filter(dataItem => dataItem !== null)
                        .map((linkHref, i) => {
                          const a = document.createElement('a');
                          a.href = linkHref;
                          a.target = "_blank";
                          a.download = i;
                          return a;
                      });
  
  links.forEach(link => {
    link.click();
  });
  // console.log(links);
})();
</script>