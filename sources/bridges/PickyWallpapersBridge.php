<?php
/**
* PickyWallpapersBridge
* Returns the latests wallpapers from http://www.pickywallpapers.com
*
* @name PickyWallpapers Bridge
* @homepage http://www.pickywallpapers.com/
* @description Returns the latests wallpapers from PickyWallpapers
* @maintainer nel50n
* @update 2014-03-31
* @use1(c="category",s="subcategory",m="max number of wallpapers",r="resolution (1920x1200, 1680x1050, ...)")
*/
class PickyWallpapersBridge extends BridgeAbstract {

    private $category;
    private $subcategory;
    private $resolution;

    public function collectData(array $param){
        $html = '';
        if (!isset($param['c'])) {
            $this->returnError('You must specify at least a category (?c=...).', 400);
        } else {
            $baseUri = 'http://www.pickywallpapers.com';

            $this->category = $param['c'];
            $this->subcategory = $param['s'] ?: '';
            $this->resolution = $param['r'] ?: '1920x1200';    // Wide wallpaper default

            $num = 0;
            $max = $param['m'] ?: 12;
            $lastpage = 1;

            for ($page = 1; $page <= $lastpage; $page++) {
                $link = $baseUri.'/'.$this->resolution.'/'.$this->category.'/'.(!empty($this->subcategory)?$this->subcategory.'/':'').'page-'.$page.'/';
                $html = file_get_html($link) or $this->returnError('No results for this query.', 404);

                if ($page === 1) {
                    preg_match('/page-(\d+)\/$/', $html->find('.pages li a', -2)->href, $matches);
                    $lastpage = min($matches[1], ceil($max/12));
                }

                foreach($html->find('.items li img') as $element) {

                    $item = new \Item();
                    $item->uri = str_replace('www', 'wallpaper', $baseUri).'/'.$this->resolution.'/'.basename($element->src);
                    $item->timestamp = time();
                    $item->title = $element->alt;
                    $item->thumbnailUri = $element->src;
                    $item->content = $item->title.'<br><a href="'.$item->uri.'">'.$element.'</a>';
                    $this->items[] = $item;

                    $num++;
                    if ($num >= $max)
                        break 2;
                }
            }
        }
    }

    public function getName(){
        return 'PickyWallpapers - '.$this->category.(!empty($this->subcategory) ? ' > '.$this->subcategory : '').' ['.$this->resolution.']';
    }

    public function getURI(){
        return 'http://www.pickywallpapers.com';
    }

    public function getCacheDuration(){
        return 43200; // 12 hours
    }
}
