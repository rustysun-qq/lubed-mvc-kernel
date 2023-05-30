<?php
namespace Lubed\MVCKernel;

abstract class AbstractView {
    private $modelAndView;


    public function getModelAndView() {
        return $this->modelAndView;
    }

    /**
     * Constructor.
     *
     * @param ModelAndView $modelAndView Model to render.
     *
     * @return void
     */
    protected function __construct(ModelAndView $modelAndView) {
        $this->modelAndView=$modelAndView;
    }
}