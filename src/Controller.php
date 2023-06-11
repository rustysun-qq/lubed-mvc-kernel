<?php
namespace Lubed\MVCKernel;
interface Controller {
    public function init();

    public function setView($view);
}