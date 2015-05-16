<?php
/**
 *
* @name Tuxboard
* @homepage http://www.tuxboard.com/
* @description Tuxboard
* @update 2014-07-08
* initial maintainer: superbaillot.net
 */
class TuxboardBridge extends BridgeAbstract{

    public function collectData(array $param){

    function StripCDATA($string) {
    	$string = str_replace('<![CDATA[', '', $string);
    	$string = str_replace(']]>', '', $string);
    	return $string;
    }

    function ExtractContent($url) {
	$html2 = file_get_html($url);
	$text = $html2->find('article#page', 0)->innertext;
	$text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);
	return $text;
    }

        $html = file_get_html('http://www.tuxboard.com/feed/atom/') or $this->returnError('Could not request Tuxboard.', 404);
	$limit = 0;

	foreach($html->find('entry') as $element) {
	 if($limit < 10) {
	 $item = new \Item();
	 $item->title = StripCDATA($element->find('title', 0)->innertext);
	 $item->uri = $element->find('link', 0)->href;
	 $item->timestamp = strtotime($element->find('published', 0)->plaintext);
	 $item->content = ExtractContent($item->uri);
	 $this->items[] = $item;
	 $limit++;
	 }
	}

       
        
    }

    public function getName(){
        return 'Tuxboard';
    }

    public function getURI(){
        return 'http://www.tuxboard.com';
    }

    public function getDescription(){
        return 'Tuxboard via rss-bridge';
    }

    public function getCacheDuration(){
        return 3600; // 1 hour
    }
}
?>
