<?php

/**
 * @name Zataz
 * @homepage http://www.zataz.com/
 * @description ZATAZ Magazine - S'informer, c'est déjà se sécuriser
 * @maintainer aledeg
 * @update 07/02/2015
 */
class ZatazBridge extends BridgeAbstract {

	public function collectData(array $param) {
		$html = file_get_html($this->getURI()) or $this->returnError('Could not request ' . $this->getURI(), 404);

		$recent_posts = $html->find('#recent-posts-3', 0)->find('ul', 0)->find('li');
		foreach ($recent_posts as $article) {
			if (count($this->items) < 5) {
				$uri = $article->find('a', 0)->href;
				$this->items[] = $this->getDetails($uri);
			}
		}
	}

	private function getDetails($uri) {
		$html = file_get_html($uri) or exit;

		$item = new \Item();

		$article = $html->find('.gdl-blog-full', 0);
		$item->uri = $uri;
		$item->title = $article->find('.blog-title', 0)->find('a', 0)->innertext;
		$item->content = $article->find('.blog-content', 0)->innertext;
		$item->timestamp = $this->getTimestampFromDate($article->find('.blog-date', 0)->find('a', 0)->href);
		return $item;
	}

	private function getTimestampFromDate($uri) {
		preg_match('/\d{4}\/\d{2}\/\d{2}/', $uri, $matches);
		$date = new \DateTime($matches[0]);
		return $date->format('U');
	}

	public function getName() {
		return 'Zataz Magazine';
	}

	public function getCacheDuration() {
		return 7200; // 2h
	}

	public function getURI() {
		return 'http://www.zataz.com';
	}

}
