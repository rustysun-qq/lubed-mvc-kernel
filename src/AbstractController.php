<?php
namespace Lubed\MVCKernel;

abstract class AbstractController implements Controller
{
	protected $request;
	protected $view;

	private function __clone()
	{}

	public function __construct($request)
	{
        $this->request = $request;
        $this->init();
    }

	public function init()
	{

	}

	public function setView($view)
	{
		$this->view = $view;
	}
}