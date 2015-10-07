<?php

interface SuggestedRelatedItemsInterface
{

    /**
     * Suggests items without taking existing items into account
     * @return array suggested items
     */
    public function suggestedRelatedItems($relation);

    function belongsToSameItem($relation, CActiveRecord $suggestedRelatedItem, CActiveRecord $existingRelatedItem);

    /**
     * Takes into account existing items
     */
    public function suggestedUpdatedRelatedItems($relation);

    //function unsetMatchingRelatedItemInArray(&$items, $q);

    function searchRelatedItemArray(&$items, $q);

    /**
     * For debug
     * @return mixed
     */
    public function getSuggestedUpdatedRelatedItemsLog($relation);

    /**
     * For debug
     * @return mixed
     */
    public function printSuggestedRelatedItemsDebug($relation);

}