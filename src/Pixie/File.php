<?php

namespace Pixie;

class File
{
    /**
     * Join a set of strings into a valid filesystem path
     *
     * @param string $chunk
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public static function join($chunk)
    {
        $chunks = func_get_args();
        $chunks = array_map(function(&$chunk){
            $chunk = trim(rtrim($chunk, DIRECTORY_SEPARATOR));
            return $chunk;
        }, $chunks);

        return implode(DIRECTORY_SEPARATOR, $chunks);
    }
}
