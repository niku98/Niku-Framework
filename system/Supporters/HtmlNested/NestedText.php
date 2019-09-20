<?php
namespace System\Supporters\HtmlNested;

/**
 *
 */
class NestedText
{
	protected $items;
	protected $template;
	protected $activeCondition = null;
	public function __construct($items, $template)
	{
		$this->items = $items;
		$this->template = $template;

		return $this;
	}

	public static function prepare($items, $template)
	{
		return new static($items, $template);
	}

	public function activeCondition($callback)
	{
		$this->activeCondition = $callback;
		return $this;
	}

	public function build()
	{
		return $this->buildFromListItem($this->items);
	}

	protected function buildFromListItem($items, &$html = '', $depth = 0)
	{
		foreach ($items as $key => $item) {

			$active = $this->activeCondition != null ? $this->activeCondition : false;
			$html .= view($this->template, [
				'item' => $item,
				'depth' => $depth,
				'active' => ($active ? $active($item) : false),
			])->getLayout();
			if(method_exists($item, 'hasChildren') && $item->hasChildren()){
				$this->buildFromListItem($item->getChildren(), $html, $depth + 1);
			}
		}
		return $html;
	}
}


 ?>
