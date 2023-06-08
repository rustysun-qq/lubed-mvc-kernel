<?php
namespace Lubed\MVCKernel;

use Lubed\MVCKernel\Utils\URL;
use Lubed\Http\Uri;
use Lubed\Utils\Config;
use Lubed\Template\TemplateParser;

class HtmlView extends AbstractView
{
    public function __construct(Uri $uri,Config $path,TemplateParser $parser=NULL){
        parent::__construct($uri,$path);
        $this->renderer = new HtmlViewRenderer($path,$parser);
    }
}
