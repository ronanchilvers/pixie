<?php
/**
 * @package     Pixie
 * @copyright   Copyright (c) 2008 Ronan Chilvers (http://www.thelittledot.com)
 * @version     0.1
 * @author      Ronan Chilvers
 */

namespace Pixie;

/**
 * @package     Pixie
 * @copyright   Copyright (c) 2008 Ronan Chilvers (http://www.thelittledot.com)
 */
class String
{

    public static function Humanize($string)
    {
        $string = ucwords(strtolower($string));

        return $string;
    }

    public static function Normalize($string, $sep = '_')
    {
        $string = trim($string);
        $string = strtolower($string);
        $string = preg_replace('/\s+/', $sep, $string);

        return $string;
    }

    public static function Urlize($string)
    {
        $string     = self::Normalize($string, '-');
        $string     = preg_replace('/[^[:alnum:]\-]/', '', $string);

        return $string;
    }

    public static function Truncate($input, $length = 50)
    {
        $string = (string) $input;
        if (strlen($string) > $length) {
            $string = substr($string, 0, $length) . '&hellip;';
        }

        return $string;
    }

    public static function TruncateWords($input, $length = 50)
    {
        $string = (string) $input;
        $words  = explode(' ', $string);
        $words  = array_slice($words, 0, $length);

        return implode(' ', $words);
    }

    public static function FormatBytes($bytes)
    {
        if ($bytes < 1024) {
            return "{$bytes} B";
        }
        $bytes  = round($bytes / 1024, 4);
        if ($bytes < 1024) {
            return "{$bytes} KB";
        }
        $bytes  = round($bytes / 1024, 4);
        if ($bytes < 1024) {
            return "{$bytes} MB";
        }
        $bytes  = round($bytes / 1024, 4);
        if ($bytes < 1024) {
            return "{$bytes} GB";
        }
        $bytes  = round($bytes / 1024, 4);

        return "{$bytes} TB";

    }

    public static function TidyHtml($html)
    {
        if (!class_exists('tidy')) {
            return $html;
        }

        $config = array();
        $config['output-xhtml']                 = true;
        $config['drop-proprietary-attributes']  = true;
        $config['drop-font-tags']               = true;
        $config['drop-empty-paras']             = false;
        $config['force-output']                 = true;
        $config['bare']                         = true;
        $config['show-body-only']               = true;
        $config['word-2000']                    = true;

        $tidy   = new tidy();
        $tidy->parseString($html, $config);
        $tidy->cleanRepair();

        return (string) $tidy;
    }

    public static function CamelToHyphenated($string)
    {
        return strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $string));
    }

}
