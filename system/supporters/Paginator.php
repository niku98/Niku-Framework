<?php
namespace System\Supporters;
use \Request;

/**
 * Paginator
 */
class Paginator
{

	private $per_page;
	private $items;
	private $total_items;
	private $total_pages;
	private $current;
	private $next;
	private $previous;
	private $show_config = [
		'content' => 'pagination',
		'item' => [
			'normal' => 'page-item',
			'active' => 'active',
			'disable' => 'disabled',
			'link' => 'page-link'
		],
		'icons' => [
			'previous' => '&laquo;',
			'next' => '&raquo;'
		]
	];

	function __construct(int $per_page, int $total_items, $items)
	{
		$this->total_items = $total_items;
		$this->per_page = $per_page;
		$this->items = $items;
		$this->firstCal();

		return $this;
	}

	private function firstCal(){
		$this->totalPages(true);
		$this->current(Request::get('page') ?? 1);
		$this->previous(true);
		$this->next(true);
		return $this;
	}

	public function current($page = NULL){
		if(is_numeric($page) && $page > 0){
			$this->current = $page <= $this->total_pages ? $page : $this->total_pages;
			return $this;
		}

		return $this->current;
	}

	public function previous(bool $cal = false){
		if($cal){
			$this->previous = $this->current - 1 > 0 ? $this->current - 1 : 1;
			return $this;
		}

		return $this->previous;
	}

	public function next(bool $cal = false){
		if($cal){
			$this->next = $this->current + 1 <= $this->total_pages ? $this->current + 1 : $this->total_pages;;
			return $this;
		}
		return $this->next;
	}

	public function totalPages(bool $cal = false){
		if($cal){
			if($this->total_items % $this->per_page == 0)
				$this->total_pages = $this->total_items / $this->per_page;
			else {
				$this->total_pages = (int)($this->total_items / $this->per_page + 1);
			}
			return $this;
		}
		return $this->total_pages;
	}

	public function items($items= [])
	{
		if(!empty($items)){
			$this->items = $items;
			return $this;
		}
		return $this->items;
	}

	public function show_config(array $config = [])
	{
		if(!empty($config)){
			$this->show_config = $config;
			return $this;
		}

		return $this->show_config;
	}

	public function contentClass(string $class){
		if(!empty($class)){
			$this->show_config['content'] = $class;
			return $this;
		}
		return $this->show_config['content'];
	}

	public function itemClass(array $class = []){
		if(!empty($class)){
			$this->show_config['item'] = $class;
			return $this;
		}
		return $this->show_config['item'];
	}

	public function itemActiveClass(string $class = ''){
		if(!empty($class)){
			$this->show_config['item']['active'] = $class;
			return $this;
		}

		return $this->show_config['item']['active'];
	}

	public function itemDefaultClass(string $class = ''){
		if(!empty($class)){
			$this->show_config['item']['normal'] = $class;
			return $this;
		}

		return $this->show_config['item']['normal'];
	}

	public function itemLinkClass(string $class = ''){
		if(!empty($class)){
			$this->show_config['item']['link'] = $class;
			return $this;
		}
		return $this->show_config['item']['link'];
	}
	public function itemDisableClass(string $class = ''){
		if(!empty($class)){
			$this->show_config['item']['disable'] = $class;
			return $this;
		}
		return $this->show_config['item']['disable'];
	}

	public function icons(array $icons = []){
		if(!empty($icons)){
			$this->show_config['icons'] = $class;
			return $this;
		}

		return $this->show_config['icons'];
	}

	public function iconsPrevious(string $icons = ''){
		if(!empty($icons)){
			$this->show_config['icons']['previous'] = $class;
			return $this;
		}

		return $this->show_config['icons']['previous'];
	}

	public function iconsNext(string $icons = ''){
		if(!empty($icons)){
			$this->show_config['icons']['next'] = $class;
			return $this;
		}

		return $this->show_config['icons']['next'];
	}

	public function pageLinkCurrent(int $page)
	{
		$url = Request::url();
		if(strpos($url, '?') !== false){
			if(strpos($url, 'page=') !== false){
				$url = preg_replace('(page=[\d]+)', 'page='.$page, $url);
			}else{
				$url .= '&page='.$page;
			}
		}else{
			$url .= '?page='.$page;
		}

		return $url;
	}

	public function pageLink(int $page, string $base = '')
	{
		if(empty($base))
			return $this->pageLinkCurrent($page);

		return $base.'?page='.$page;
	}

	public function html(int $maxPage)
	{
		if($this->total_pages < 2)
			return '';

		$maxPage = $maxPage <= $this->total_pages ? $maxPage : $this->total_pages;
		$padding = $this->total_pages - $this->current;
		if($padding < (int)($maxPage/2)){
			$start = $this->total_pages - $maxPage + 1 > 0 ? $this->total_pages - $maxPage + 1 : 1;
		}else{
			$start = $this->current - (int)($maxPage/2) > 0 ? $this->current - (int)($maxPage/2) : 1;
		}

		$end = $start + $maxPage - 1 < $this->total_pages ? $start + $maxPage - 1 : $this->total_pages;
		$html = '<ul class="'.$this->show_config['content'].'">';

		if($this->previous <= $this->current - 1)
			$html .= '<li class="'.$this->itemDefaultClass().'"><a href="'.$this->pageLink($this->previous).'" class="'.$this->itemLinkClass().'">'.$this->iconsPrevious().'</a></li>';

		for ($i=$start; $i <= $end; $i++) {
			$classes = $this->itemDefaultClass();
			if($i == $this->current)
				$classes .= ' '.$this->itemActiveClass();

			$html .= '<li class="'.$classes.'"><a href="'.$this->pageLink($i).'" class="'.$this->itemLinkClass().'">'.$i.'</a></li>';
		}

		if($this->next >= $this->current + 1)
			$html .= '<li class="'.$this->itemDefaultClass().'"><a href="'.$this->pageLink($this->next).'" class="'.$this->itemLinkClass().'">'.$this->iconsNext().'</a></li>';

		$html .= '</ul>';

		return $html;
	}
}


 ?>
