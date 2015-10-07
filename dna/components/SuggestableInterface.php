<?php

interface SuggestableInterface extends ComparableInterface
{
    /**
     * Returns the corresponding existing item based on another item's properties
     * @param $item
     * @return mixed
     */
    function findByItem(CActiveRecord $item);
}
