<?php
namespace Lubed\MVCKernel;

use stdClass;
use Lubed\Template\{TemplatePath,TemplateCompiler,DefaultCompiler,TemplateParser};
use Lubed\Utils\{Buffer,Config};

class HtmlViewRenderer implements ViewRenderer {
    private $suffix = '.html';
    private $compiler;
    private $path;
    private $data;

    public function __construct(Config $path,TemplateParser $parser)
    {
        $template_path = new TemplatePath($path->get('source'),$path->get('cached'));
        $this->compiler = new DefaultCompiler($template_path, $parser);
        $this->data = [
            'layouts' => [],
            'blocks'  => [],
            'vars'    => [],
        ];
        $this->renderer = new stdClass();
        $this->init();
    }

    public function assign(string $name, $value = NULL): void {
        if ('view' !== $name) { //protected 'view' variable.
            if (is_array($name) && $name) {
                unset($name['view']);
                $this->data['vars'] = array_merge($this->data['vars'], $name);
            } else {
                $this->data['vars'][$name] = $value;
            }
        } else {
            //TODO: throw execption.
        }
    }

    public function getBlockName(string $name):string
    {
        return sprintf('%s%s%s',$this->renderer->blockPre,$name,$this->renderer->blockSuf);
    }

    public function load(string $view):void
    {
        $viewFile = $view . $this->suffix;
        $compiler = $this->compiler;
        $data = $this->data['vars']??[];
        $this->renderer->loadViewFile($view_file, $data, $compiler);
    }

    public function render(string $view):string
    {
        $view_file = sprintf('%s%s', $view, $this->suffix);
        $compiler = $this->compiler;
        $data = $this->data;
        $buffer = new Buffer();
        //renderer closure
        $fn_renderer = function(object $renderer,string $view_file,array $data,TemplateCompiler $compiler,Buffer $buffer){

            return $renderer->renderViewFile($view_file,$data,$compiler,$buffer);
        };
        //render view file
        return $fn_renderer($this->renderer,$view_file,$data,$compiler,$buffer);
    }

    private function init(){
        $this->renderer->blockPre='%%BLOCK__';
        $this->renderer->blockSuf='__BLOCK%%';
        $this->renderer->renderViewFile = function(string $filename,?array $vars){
            if (!$filename) {
                return;
            }

            if (NULL !== $vars) {
                extract($vars);
            }

            require $filename;
        };

        $this->renderer->loadViewFile=function(string $view_file, array $data, TemplateCompiler $compiler){
            $file = str_replace('\\', '/', $view_file);
            $compiled_file = $compiler->compile($file);
            $this->renderFile($compiled_file,$data);
        };

        $this->renderer->renderViewFile=function(string $view_file, array $data, TemplateCompiler $compiler, Buffer $buffer){
            $compiled_file = $compiler->compile($view_file);
            $buffer::start();
            $this->renderFile($compiled_file);
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
