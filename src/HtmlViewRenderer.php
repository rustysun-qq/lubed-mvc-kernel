<?php
namespace Lubed\MVCKernel;

use stdClass;
use Lubed\Template\{TemplatePath,TemplateCompiler,DefaultCompiler,TemplateParser};
use Lubed\Utils\{OutputBuffer,Config};

class HtmlViewRenderer implements ViewRenderer {
    private $suffix = '.php';
    private $compiler;
    private $path;
    private $data;

    public function __construct(Config $path,TemplateParser $parser,array &$data)
    {
        $this->path = new TemplatePath($path->get('source'),$path->get('cached'));
        $this->compiler = new DefaultCompiler($this->path, $parser);
        $this->data = $data;
        $this->renderer = new stdClass();
        $this->init();
    }

    public function getBlockName(string $name):string
    {
        return sprintf('%s%s%s',$this->renderer->blockPre,$name,$this->renderer->blockSuf);
    }

    public function load(string $view):void
    {
        $view_file = $view . $this->suffix;
        $compiler = $this->compiler;
        $data = $this->data;
        ($this->renderer->loadViewFile)($view_file, $data, $compiler,$this->renderer->renderer);
    }

    public function render(string $view):string
    {
        $view_file = sprintf('%s/%s%s',$this->path->getSourcePath(), $view, $this->suffix);
        $compiler = $this->compiler;
        $data = $this->data;
        $buffer = new OutputBuffer();
        //renderer closure
        $fn_renderer = function(object $renderer,string $view_file,array $data,TemplateCompiler $compiler,object $buffer){
            return ($renderer->renderer)($view_file,$data['vars']??[],$compiler,$buffer);
        };
        //render view file
        return $fn_renderer($this->renderer,$view_file,$data,$compiler,$buffer);
    }

    public function setSuffix(string $suffix):void{
        $this->suffix=$suffix;
    }

    private function init(){
        $this->renderer->blockPre='%%BLOCK__';
        $this->renderer->blockSuf='__BLOCK%%';
        $this->renderer->renderer = function(string $filename,?array $vars){
            if (!$filename) {
                return;
            }

            if (NULL !== $vars) {
                extract($vars);
            }

            require $filename;
        };

        $this->renderer->loadViewFile=function(string $view_file, array $data, TemplateCompiler $compiler,$renderer){
            $file = str_replace('\\', '/', $view_file);
            $compiled_file = $compiler->compile($file);
            $renderer($compiled_file,$data);
        };

        $this->renderer->renderViewFile=function(string $view_file, array $data, TemplateCompiler $compiler, OutputBuffer $buffer,$renderer){
            $compiled_file = $compiler->compile($view_file);
            $buffer::start();
            $renderer($compiled_file,$data);
            $result = $buffer::getAndClean();
            //parse layout
            $keys = array_keys($data['layouts']);
            foreach ($keys as $key => $value) {
                $keys[$key] = $this->blockPre . $value . $this->blockSuf;
                unset($data['blocks'][$value]);
            }
            $values = array_values($data['layouts']);
            $result = str_replace($keys, $values, $result);
            //parse block
            $keys = array_keys($data['blocks']);
            foreach ($keys as $key => $value) {
                $keys[$key] = $this->blockPre . $value . $this->blockSuf;
            }
            $values = array_values($data['blocks']);
            return str_replace($keys, $values, $result);
        };
    }
}
