<?php
namespace Lubed\MVCKernel;
use Lubed\Http\Request;
use Lubed\HttpApplication\RedirectResponse;

abstract class AbstractController implements Controller {
    /**
     * @var Request $request
     */
    protected $request;
    protected $view;

    private function __clone() {
    }

    public function __construct($request) {
        $this->request=$request;
        $this->init();
    }

    abstract function init();

    public function setView($view) {
        $this->view=$view;
    }

    protected function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }
}