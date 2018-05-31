
<?php require_once('simplehtmldom_1_5/simple_html_dom.php') ?>
<?php
  ini_set('max_execution_time', 300);
?>
<?php
	$base	= 'http://tooxclusive.com/main/download-mp3/';
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_URL, $base);
	curl_setopt($curl, CURLOPT_REFERER, $base);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$str = curl_exec($curl);
	curl_close($curl);

	// Create a DOM object
	$dom = new simple_html_dom();
	// Load HTML from a string
	$dom->load($str);

  $musicLinks = [];
  $postString = 'div[id^="post-"]';
  $posts = $dom->find($postString);
  
  foreach($posts as $key=>$node){
    $text = $node->find('h2 a')[0]->innertext;
    $artistAndSong = trim(preg_replace('/^\s*\[(\w+)\]\s*/i',"", $text));

    echo 'Artist & Song : '.$artistAndSong.'<br />';
    $text1 = str_replace('’', '',$text);
		$songLinks = str_replace(" ","-",trim(strtolower(preg_replace("/[\W|\d+]+/", " ", $text1))));
					
		$url = "http://tooxclusive.com/download-mp3/$songLinks";
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
		
    $song = $musicPage->find('a[href*="uploads"]', 0);
    echo 'Song Links : '.$song.'<br /><br />';
    
    if (isset($song->href)) {
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