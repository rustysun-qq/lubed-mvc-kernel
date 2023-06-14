<?php
namespace Lubed\MVCKernel\Views;
final class Tpl {
    private $name;
    private $suffix;
    private $data=[];

    public function __construct($path, string $suffix='.html') {
        $this->path=$path;
        $this->suffix=$suffix;
    }

    public function clearData() {
        $this->data=[];
        return $this;
    }

    public function load(string $name) {
        $require=function(string $path, array $vars) {
            extract($vars);
            require $path;
        };
        //
        $path=$this->getTplFilePath($name);
        $require($path, $this->data);
    }

    public function setData(array $data) {
        $this->data=array_merge($this->data, $data);
    }

    private function getTplFilePath(string $tpl_name) {
        return vsprintf('%s/%s%s', [
            $this->path->get('source'),
            $tpl_name,
            $this->suffix
        ]);
    }
}