<?php
namespace system\supporters;
/**
* Base Controller
* Parent Controller, another Controller will extend this Controller
*/
class Controller
{
	protected $view;
	protected $model;
	protected $data = [];

	public function getModelName(){
		return get_class($this->model);
	}

	public function showView($viewName, $data = []){
		return $this->view->setLayout($viewName, !empty($data) ? $data : $this->data);
	}
}


?>
