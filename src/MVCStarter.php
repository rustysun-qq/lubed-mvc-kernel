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
         $path = $view_config->get('path');
         $parser_class = $view_config->get('parser');
         $parser=null;
         if($parser_class && false !== class_exists($parser_class)){
         	  	$parser=new $parser_class();
         }
         if (!$parser) {
				MVCExceptions::missRequiredArgument('invalid view parser!');
         }
         $view = new $view_class($request->getUri(),$path,$parser);
         $kernel->registerView($view);
     }
     $this->app->instance('kernel',$kernel);
	}
}
