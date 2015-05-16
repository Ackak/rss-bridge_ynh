<?php
/**
* RssBridgeRue89
* Returns the 5 newest posts from http://rue89.nouvelobs.com/ (full text)
*
* @name Rue89
* @description Returns the 5 newest posts from Rue89 (full text)
* @update 2015-01-30
* @maintainer pit-fgfjiudghdf
*/
class Rue89Bridge extends BridgeAbstract{
    public function collectData(array $param){
    function Rue89StripCDATA($string) {
        $string = str_replace('<![CDATA[', '', $string);
        $string = str_replace(']]>', '', $string);
        return $string;
    }
    function Rue89ExtractContent($url) {
        $html2 = file_get_html($url);
        //$text = $html2->find('div[class=text]', 0)->innertext;

	foreach($html2->find('img') as $image) {
		$image->src = $image->getAttribute('data-src');
	}
        $text = $html2->find('div.content', 0)->innertext;


	$text = str_replace('href="/', 'href="http://rue89.nouvelobs.com/', $text);
	$text = str_replace('src="/', 'src="http://rue89.nouvelobs.com/', $text);
	$text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);
	$text = strip_tags($text, '<h1><h2><strong><p><b><a><blockquote><img><em><ul><ol>');
        return $text;
    }
        $html = file_get_html('http://rue89.feedsportal.com/c/33822/f/608948/index.rss') or $this->returnError('Could not request Rue89.', 404);
        $limit = 0;
        foreach($html->find('item') as $element) {
         if($limit < 5) {
         $item = new \Item();
         $item->title = Rue89StripCDATA($element->find('title', 0)->innertext);
         $item->uri = str_replace('#commentaires', '', Rue89StripCDATA($element->find('comments', 0)->plaintext));
         $item->timestamp = strtotime($element->find('pubDate', 0)->plaintext);
         $item->content = Rue89ExtractContent($item->uri);
         $this->items[] = $item;
         $limit++;
         }
        }

    }
    public function getName(){
        return 'Rue89';
    }
    public function getURI(){
        return 'http://rue89.nouvelobs.com/';
    }
    public function getCacheDuration(){
        return 3600; // 1 hour
    }
}
