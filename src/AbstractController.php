<?php
namespace Lubed\MVCKernel;

abstract class AbstractController implements Controller
{
	protected $request;

	public function __construct()
	{
	}

	public function init($request){
		$this->request = $request;
	}
}