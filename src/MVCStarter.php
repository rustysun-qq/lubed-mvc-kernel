<?php
namespace Lubed\MVCKernel;

use Lubed\Utils\Config;
use Lubed\Container\Container;

final class MVCStarter
{
    private $config;
	private $app;

	public function __construct(Config $config,Container &$app)
	{
		$this->config = $config;
		$this->app = $app;
	}

	public function start()
	{
     $request = $this->app->get('lubed_http_request');
     $kernel = new DefaultKernel();
     $kernel->setRequest($request);
     $view_config = $this->config->get('view');

     if ($view_config) {
         $view_class = $view_config->get('class');
         $this->app->alias($view_class,'view');
         $path = $view_config->get('path');
         $suffix = $view_config->get('suffix');
         $view = new $view_class($path,[],$suffix);
         $this->app->instance('view',$view);
         $kernel->registerView($view);
     }
     $this->app->instance('kernel',$kernel);
	}
}
