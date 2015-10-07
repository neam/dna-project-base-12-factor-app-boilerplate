<?php

interface ComparableInterface
{
    /**
     * @param ComparableInterface $other
     * @param String $comparison any of ==, <, >, =<, >=, etc
     * @return Bool true | false depending on result of comparison
     */
    public function compareTo(ComparableInterface $other, $comparison);
}
