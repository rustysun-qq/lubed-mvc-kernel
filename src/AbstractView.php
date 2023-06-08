<?php
namespace Lubed\MVCKernel;

use lubed\Http\Uri;
use Lubed\MVCKernel\Utils\URL;
use Lubed\Utils\{Buffer,Config};

abstract class AbstractView {
    protected $renderer;
    protected $path;
    protected $curLayout = NULL, $curBlock;
    protected $data      = [
        'layouts' => [],
        'blocks'  => [],
        'vars'    => [],
    ];
    protected $uri;
    protected $compiler;

    public function __construct(Uri $uri,Config $path) {
        $this->data['vars'] = [
            'view' => &$this,
        ];
        $this->setPath($path);
        $this->uri = $uri;
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

    public function end() {
        Buffer::clean();
    }

    public function beginBlock($block_name, $val = NULL) {
        $block_name = strtoupper($block_name);
        $this->curBlock = $block_name;
        if (NULL !== $val) {
            $this->data['blocks'][$this->curBlock] = $val;
        }
        Buffer::start();
    }

    public function endBlock() {
        $content = Buffer::getAndClean();
        if (!isset($this->data['blocks'][$this->curBlock])) {
            echo $this->renderer->getBlockName($this->curBlock);
        }
        $this->data['blocks'][$this->curBlock] = trim($content);
    }

    public function beginLayout($block_name) {
        $block_name = strtoupper($block_name);
        if (isset($this->data['blocks'][$block_name])) {
            $this->curLayout = $block_name;
            Buffer::start();
        } else {
            $this->curLayout = NULL;
        }
    }

    public function endLayout() {
        if (NULL === $this->curLayout) {
            //TODO:
            //return FALSE;
        }
        $content = Buffer::getAndClean();
        $this->data['layouts'][$this->curLayout] = trim($content);
        $this->curLayout = NULL;
    }

    public function load(string $view):string{
        $this->renderer->load($view);
    }

    public function place(string $block) {
        $block_name = strtoupper($block);
        if (!isset($this->data['blocks'][$block_name])) {
            $this->data['blocks'][$blockName] = '';
            echo $this->renderer->getBlockName($block_name);
        }
    }

    public function setPath(Config $path) {
        $this->path = $path;
    }

    public function url(string $path = '', array $params = [], string $site = '') {
        if (is_string($params)) {
            $site = $params;
            $params = [];
        }
        URL::setDomain($this->uri->getHost(), $this->uri->getMainDomain());
        $url = URL::create($path, $site, $params);
        return $url;
    }

    public function escape(string $raw):string {
        $flags = ENT_QUOTES;

        if (defined('ENT_SUBSTITUTE') && !defined('HHVM_VERSION')) {
            $flags |= ENT_SUBSTITUTE;
        } else {
            $flags |= ENT_IGNORE;
        }

        return htmlspecialchars($raw, $flags, "UTF-8");
    }

    public function escapeButPreserveUris(string $raw):string {
        $escaped = $this->escape($raw);
        return preg_replace("@([A-z]+?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@", "<a href=\"$1\" target=\"_blank\">$1</a>", $escaped);
    }

    public function slug(string $original):string {
        $slug = str_replace(" ", "-", $original);
        $slug = preg_replace('/[^\w\d\-\_]/i', '', $slug);
        return strtolower($slug);
    }
}
