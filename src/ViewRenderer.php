<?php
namespace Lubed\MVCKernel;

interface ViewRenderer {
    public function getBlockName(string $name):string;
    public function load(string $view_file):void;
    public function render(string $view):string;
}
