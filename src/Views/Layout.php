<?php
namespace Lubed\MVCKernel\Views;

use Lubed\MVCKernel\MVCExceptions;

class Layout {
    protected $tpl=null;
    protected $name='';
    protected $data=[];
    private $blocks=[];
    private $curLevel=0;
    private $blockQueue=[];
    private $blockLevelMap=[];
    private $blockPre='%%BLOCK__';
    private $blockSuf='__BLOCK%%';

    public function __construct(Tpl $tpl, string $name) {
        $this->tpl=$tpl;
        $this->name=$name;
        $this->blocksParser=function ($tpl,$layout,array $blocks_level_map,array $all_blocks) {
            if (!$blocks_level_map||!$all_blocks) {
                return $tpl;
            }
            $len=count($blocks_level_map);
            for ($i=0; $i < $len; $i++) {
                if (!isset($blocks_level_map[$i])) {
                    continue;
                }
                $level = $blocks_level_map[$i];
                if (!$level) {
                    continue;
                }
                $result=[];
                foreach ($level as $blockName) {
                    if (!isset($all_blocks[$blockName])) {
                        continue;
                    }
                    $key=$layout->getFullBlockName($blockName);
                    $val=$all_blocks[$blockName];
                    $result[$key]=$val;
                    unset($all_blocks[$blockName]);
                }
                if (!empty($result)) {
                    $tpl= str_replace(array_keys($result), array_values($result), $tpl);
                }
            }
            return $tpl;
        };
    }

    public function render() {
        ob_start();
        $this->load($this->name);
        $html=ob_get_clean();
        return ($this->blocksParser)($html,$this,$this->blockLevelMap,$this->blocks);
    }

    public function extend(string $name) {
        $this->load($name);
    }

    public function load(string $name) {
        $this->tpl->load($name,$this->data);
    }

    public function beginBlock($blockName) {
        $blockName=strtoupper($blockName);
        $parentBlock=$this->getCurrentBlock();
        if ($parentBlock == $blockName) {
            MVCExceptions::invalidBlock('子block与父block不允许重名,block名称：' .
                $blockName, ['method'=>__METHOD__]);
        }
        $this->curLevel++;
        array_push($this->blockQueue, $blockName);
        ob_start();
        return true;
    }

    public function endBlock($blockName=null) {
        $this->curLevel--;
        $curBlock=array_pop($this->blockQueue);
        if ($blockName && $curBlock !== strtoupper($blockName)) {
            MVCExceptions::invalidBlock(sprintf('block数量不匹配，(%s vs %s).', strtolower($curBlock), $blockName), ['method'=>__METHOD__]);
        }
        $content=ob_get_clean();
        $content=trim($content);
        if (isset($this->blocks[$curBlock])) {
            $this->blocks[$curBlock]=$content;
            return true;
        }
        $this->blocks[$curBlock]=$content;
        $this->addBlockToLevelMap($curBlock, $this->curLevel);
        echo $this->getFullBlockName($curBlock);
    }

    public function place($blockName, $content='') {
        $blockName=strtoupper($blockName);
        $parentBlock=$this->getCurrentBlock();
        if ($parentBlock == $blockName) {
            MVCExceptions::invalidBlock(sprintf('block(%s)不允许重名', $blockName), ['method'=>__METHOD__]);
        }
        if (isset($this->blocks[$blockName])) {
            $this->blocks[$blockName]=$content;
            return true;
        }
        $this->blocks[$blockName]=$content;
        $this->addBlockToLevelMap($blockName, $this->curLevel);
        echo $this->getFullBlockName($blockName);
    }

    public function getCurrentBlock() {
        if (!$this->blockQueue) {
            return null;
        }
        $lastIdx=count($this->blockQueue) - 1;
        return $this->blockQueue[$lastIdx];
    }

    private function addBlockToLevelMap($blockName, $level=0) {
        if (!isset($this->blockLevelMap[$level])) {
            $this->blockLevelMap[$level]=[];
        }
        array_push($this->blockLevelMap[$level], $blockName);
    }

    private function getFullBlockName(string $name) : string {
        return sprintf('%s%s%s', $this->blockPre, $name, $this->blockSuf);
    }

}