<?php

class PHPExcel_Reader_Excel2007_XNamespace extends PHPExcel_Reader_Excel2007
{

    public function securityScan($xml)
    {
        $xml = parent::securityScan($xml);
        return str_replace(['<x:', '</x:'], ['<', '</'], $xml);
    }

}