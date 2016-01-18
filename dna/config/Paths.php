<?php

class Paths
{
    static public function root()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
    }

    static public function dna()
    {
        return static::root() . DIRECTORY_SEPARATOR . 'dna';
    }
}
