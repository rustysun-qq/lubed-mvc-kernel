<?php
namespace Lubed\MVCKernel\Views;
use Lubed\MVCKernel\View;

class HtmlView implements View {
    protected $data;
    protected $suffix;
    protected $layout;

    public function __construct($path,array $data=[],string $suffix='.html') {
        $this->tpl=new Tpl($path,$suffix);
        $this->setData( $data);
        $this->suffix=$suffix;
    }

    public function load(string $name){
        $this->layout=new Layout($this->tpl,$name);
        return $this;
    }

    public function display(string $name, array $data=[]) {
        $view=new self($name, $data,$this->suffix);
        return trim($view->render(), " \r\n");
    }

    public function render() {
        $this->tpl->clearData()->setData($this->getData());
        return $this->layout->render();
    }

    public function setData(array $data){
        $this->data = $data;
        return $this;
    }

    private function getData() {
        $loaders=[
            //'tpl'=>$this->tpl,
            'view'=>$this->layout,
        ];
        return array_merge($loaders, $this->data);
    }
}
