<?php
namespace Lubed\MVCKernel;

abstract class AbstractController implements Controller
{
	protected $request;
	protected $view;

	private function __clone()
	{}

	public function __construct()
	{}

	public function init($request)
	{
		$this->request = $request;
	}

	public function setView($view)
	{
		$this->view = $view;
	}
}