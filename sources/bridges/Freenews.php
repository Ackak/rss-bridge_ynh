<?php
/**
*
* @name Freenews
* @description Un site d'actualitÃ© pour les freenautes (mais ne parlant pas que de la freebox). Ne rentrez pas d'id si vous voulez accÃ©der aux actualitÃ©s gÃ©nÃ©rales.
* @update 26/03/2014
* @use1(id="Id de la rubrique (sans le '-')")
*/
require_once 'bridges/RssExpander.php';
define("RSS", 'http://feeds.feedburner.com/Freenews-Freebox?format=xml');
class Freenews extends RssExpander {
    public function collectData(array $param){
        $param['url'] = RSS;
        parent::collectData($param);
    }
    
    protected function parseRSSItem($newsItem) {
        $item = new Item();
        $item->title = trim($newsItem->title);
//        $this->message("item has for title \"".$item->title."\"");
        if(empty($newsItem->guid)) {
            $item->uri = $newsItem->link;
        } else {
            $item->uri = $newsItem->guid;
        }
        // now load that uri from cache
//        $this->message("now loading page ".$item->uri);
        $articlePage = str_get_html($this->get_cached($item->uri));

        $content = $articlePage->find('.post-container', 0);
        $item->content = $content->innertext;
        $item->name = $articlePage->find('a[rel=author]', 0)->innertext;
        // format should parse 2014-03-25T16:21:20Z. But, according to http://stackoverflow.com/a/10478469, it is not that simple
        $item->timestamp = $this->RSS_2_0_time_to_timestamp($newsItem);
        return $item;
    }
}
