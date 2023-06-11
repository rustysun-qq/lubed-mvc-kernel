<?php
namespace Lubed\MVCKernel\Views;
class ExceptionView extends HtmlView {

    public function load(string $name){
        $this->layout=new ExceptionLayout($this->tpl,$name);
        return $this;
    }

}
