<?php
namespace Lubed\MVCKernel;
interface View {
    public function load(string $name);

    public function render();

    public function setData(array $data);
}
