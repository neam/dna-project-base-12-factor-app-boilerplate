<?php

namespace neam;

class Sanitize
{

    static public function filename($filename)
    {
        if ($filename === "") {
            return "";
        }
        $path_parts = pathinfo($filename);
        $sanitizedFilename = \neam\URLify::filter($path_parts['filename']);
        if (empty($sanitizedFilename)) {
            $sanitizedFilename = "file";
        }
        $sanitizedExtension = "";
        if (array_key_exists('extension', $path_parts)) {
            $sanitizedExtension = \neam\URLify::filter($path_parts['extension']);
        }
        return $sanitizedFilename . ($sanitizedExtension !== "" ? ".$sanitizedExtension" : "");

    }

}