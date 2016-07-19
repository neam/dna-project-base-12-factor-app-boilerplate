<?php

namespace neam;

class Sanitize
{

    static public function filename($filename)
    {

        $path_parts = pathinfo($filename);
        $sanitizedFilename = \neam\URLify::filter($path_parts['filename']);
        if (empty($sanitizedFilename)) {
            $sanitizedFilename = "file";
        }
        $sanitizedExtension = \neam\URLify::filter($path_parts['extension']);
        return $sanitizedFilename . (!empty($sanitizedExtension) ? ".$sanitizedExtension" : "");

    }

}