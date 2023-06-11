<?php
namespace Lubed\MVCKernel\Views;

class ExceptionLayout extends Layout
{
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

    public function breakOnDelimiter(string $delimiter, string $s,string $format):string
    {
        $result=[];
        $parts = explode($delimiter, $s);
        foreach ($parts as $part) {
            $result[] = sprintf($format, $part);
        }
        return implode($delimiter, $result);
    }

    public function dump($value)
    {
        return htmlspecialchars(print_r($value, true));
    }

    /**
     * Format the args of the given Frame as a human readable html string
     *
     * @param  Frame $frame
     * @return string the rendered html
     */
    public function dumpArgs( $frame)
    {
        $html = '';
        return $html;
        //$numFrames = count($frame->getArgs());
        //
        //if ($numFrames > 0) {
        //    $html = '<ol class="linenums">';
        //    foreach ($frame->getArgs() as $j => $frameArg) {
        //        $html .= '<li>'. $this->dump($frameArg) .'</li>';
        //    }
        //    $html .= '</ol>';
        //}
        //
        //return $html;
    }
}