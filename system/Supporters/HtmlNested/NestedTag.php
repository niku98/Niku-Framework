<?php
namespace System\Supporters\HtmlNested;

/**
 *
 */
class NestedTag extends NestedText
{
	protected function buildFromListItem($items, &$html = '', $depth = 0)
	{
		return view($this->template, [
			'items' => $items,
			'activeCondition' => $this->activeCondition,
			'depth' => 0
		])->getLayout();
	}
}


 ?>
