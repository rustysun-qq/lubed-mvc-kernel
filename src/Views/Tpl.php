<?php
namespace Lubed\MVCKernel\Views;

final class Tpl
{
    private $name;
    private $suffix;
    private $data = [];

    public function __construct( $path,string $suffix='.html')
    {
        $this->path = $path;
        $this->suffix=$suffix;
    }

    public function load(string $name, array $data = [])
    {
        $path = $this->getTplFilePath($name);
        extract(array_merge($this->data, $data));
        require $path;
    }

    public function setData(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }

    public function getTplFilePath(string $tpl_name)
    {
        return vsprintf('%s/%s%s',[
            $this->path->get('source'),
            $tpl_name,
            $this->suffix
        ]);
    }
}