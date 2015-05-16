<?php
/**
* 2014-08-27
* @name NASA APOD Bridge
* @homepage http://apod.nasa.gov/apod/astropix.html
* @description Returns the 3 latest NASA APOD pictures and explanations
* @maintainer corenting
*/
class NasaApodBridge extends BridgeAbstract{

  public function collectData(array $param) {

    $html = file_get_html('http://apod.nasa.gov/apod/archivepix.html') or $this->returnError('Error while downloading the website content', 404);
    $list = explode("<br>", $html->find('b', 0)->innertext);

    for($i = 0; $i < 3;$i++)
    {
      $line = $list[$i];
      $item = new \Item();

      $uri_page = $html->find('a',$i + 3)->href;
      $uri = 'http://apod.nasa.gov/apod/'.$uri_page;
      $item->uri = $uri;

      $picture_html = file_get_html($uri);
      $picture_html_string = $picture_html->innertext;

      //Extract image and explanation
      $media = $picture_html->find('p',1)->innertext;
      $media = strstr($media, '<br>');
      $media = preg_replace('/<br>/', '', $media, 1);
      $explanation = $picture_html->find('p',2)->innertext;

      //Extract date from the picture page
      $date = explode(" ", $picture_html->find('p',1)->innertext);
      $item->timestamp = strtotime($date[4].$date[3].$date[2]);

      //Other informations
      $item->content = $media.'<br />'.$explanation;
      $item->title = $picture_html->find('b',0)->innertext;
      $this->items[] = $item;
    }
  }

  public function getName(){
    return 'NASA APOD';
  }

  public function getURI(){
    return 'http://apod.nasa.gov/apod/astropix.html';
  }

  public function getCacheDuration(){
    return 3600*12; // 12 hours
  }
}
