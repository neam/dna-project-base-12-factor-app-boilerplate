<?php

/**
 * Declares data that is referenced by code directly (hard-coded) or otherwise expected by
 * frontends / users of the cms.
 *
 * Class NecessaryDbData
 */
class NecessaryDbData
{

    public static function fooTypes()
    {
        return FooType::coreTypes();
    }

    public static function barTypes()
    {
        return BarType::coreTypes();
    }

}