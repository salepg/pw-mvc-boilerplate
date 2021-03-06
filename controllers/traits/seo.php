<?php

trait SEO {
	static function __seoInitialize($obj) {
		$obj->add_view_vars($obj->get_seo_vars());
		$obj->helper('seo_rel_next_prev');
	}

	private $__seo_opts = array(
		'limit' => false,
	);

	protected function seo_set_opt($opt, $value) {
		$this->__seo_opts[$opt] = $value;
	}

	function get_seo_vars() {
		return array(
			'seo_title'   => $this->get_seo_title(),
			'seo_desc'    => $this->get_seo_desc(),
			'seo_noindex' => $this->get_seo_noindex(),
		);
	}

	function seo_rel_next_prev() {
		$page =& $this->page;
		$input =& $this->input;
		$config =& $this->config;
		$html = '';
		$limit = $this->__seo_opts['limit'];
		$href = 'http://' . $config->httpHost . $page->url;

		if (empty($limit)) {
			return $html;
		}

		if ($page->pageNum > 1) {
			$html .= '<link rel="prev" href="';
			$html .= $href . ($input->pageNum > 2 ? $config->pageNumUrlPrefix . ($input->pageNum - 1) : '');
			$html .= '">';
		}

		if ($input->pageNum * $limit < $page->children->count) {
			$html .= '<link rel="next" href="';
			$html .= $href . $config->pageNumUrlPrefix;
			$html .= ($input->pageNum + 1) . '">';
		}

		return $html;
	}

	protected function get_seo_title() {
		$page =& $this->page;

		if (!($seo_title = $page->seo_title)) {
			$seo_title = $page->title;

			if (method_exists($this, 'setting') && $this->setting('site_name')) {
				return $seo_title . ' ' . $this->get_seo_separator()  . ' ' . $this->setting('site_name');
			}
		}

		return $seo_title;
	}

	protected function get_seo_desc() {
		$page =& $this->page;
		$input =& $this->input;

		/**
		 * don't output meta description if we are using pagination
		 * - http://moz.com/blog/pagination-best-practices-for-seo-user-experience
		 */
		if ($page->seo_description && $input->pageNum < 2) {
			return "<meta name='description' content='{$page->seo_description}'>";
		}

		return false;
	}

	protected function get_seo_noindex() {
		$page =& $this->page;

		if ($page->seo_noindex) {
			return "<meta name='robots' content='noindex, nofollow'>";
		}

		return false;
	}

	private function get_seo_separator() {
		$page =& $this->page;

		if (!($seo_deparator = $page->seo_separator)) {
			$seo_separator = '|';
		}

		return $seo_separator;
	}
}



