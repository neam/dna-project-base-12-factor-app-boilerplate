<?php

trait SuggestedRelatedItemsTrait
{

    public $suggestedUpdatedRelatedItemsLog = null;

    /**
     * Takes into account existing related items
     */
    public function suggestedUpdatedRelatedItems($relation)
    {
        $this->suggestedUpdatedRelatedItemsLog = array();
        $currentRelatedItems = $this->$relation;
        $suggestedRelatedItems = $this->suggestedRelatedItems($relation);
        $suggestedUpdatedRelatedItems = $currentRelatedItems;
        foreach ($suggestedRelatedItems as $suggestedRelatedItem) {

            // If already exists, check if belongs to this or not
            $relations = $this->relations();
            $relatedItemClass = $relations[$relation][1];
            $existingRelatedItem = $relatedItemClass::model()->findByItem($suggestedRelatedItem);
            if (!empty($existingRelatedItem)) {
                if ($this->belongsToSameItem($relation, $suggestedRelatedItem, $existingRelatedItem)) {
                    // already belongs to current item - we do nothing and keep it as it was - TODO: Enable suggested updated attributes
                    //$this->suggestedUpdatedRelatedItemsLog[] = "$relatedItemClass '$existingRelatedItem' already belongs to current item - we do nothing and keep it as it was";
                } else {
                    // belongs to another item - add to current item instead
                    $this->suggestedUpdatedRelatedItemsLog[] = "$relatedItemClass '$existingRelatedItem' which belonged to another item " /*. "{$existingRelatedItem->node_id}"*/ . " will be removed from that item and attached to this item instead";
                    $suggestedRelatedItem->id = $existingRelatedItem->id;
                    $suggestedUpdatedRelatedItems[] = $suggestedRelatedItem;
                }
            } else {
                // check if we already have it amongst suggested related items
                if ($alreadySuggestedIdx = $this->searchRelatedItemArray(
                        $suggestedUpdatedRelatedItems,
                        $suggestedRelatedItem
                    ) !== null
                ) {
                    // already suggested previously - a potential conflict
                    $this->suggestedUpdatedRelatedItemsLog[] = "$relatedItemClass '$suggestedRelatedItem' was suggested more than once and will only be added once";
                } else {
                    // If not exists and not already suggested, add as suggested updated related item
                    $this->suggestedUpdatedRelatedItemsLog[] = "$relatedItemClass '$suggestedRelatedItem' will be added and attached to this item";
                    $suggestedUpdatedRelatedItems[] = $suggestedRelatedItem;
                }
            }

        }

        return $suggestedUpdatedRelatedItems;
    }

    /*
    protected function unsetMatchingRelatedItemInArray(&$relatedItems, $q)
    {
        foreach ($relatedItems as $k => $relatedItem) {
            if ($relatedItem->compareTo($q, "==")) {
                unset($relatedItems[$k]);
            }
        }
        return null;
    }
    */

    public function searchRelatedItemArray(&$relatedItems, $q)
    {
        foreach ($relatedItems as $k => $relatedItem) {
            if ($relatedItem->compareTo($q, "==")) {
                return $k;
            }
        }
        return null;
    }

    /**
     * For debug
     * @return mixed
     */
    public function getSuggestedUpdatedRelatedItemsLog($relation)
    {
        return $this->suggestedUpdatedRelatedItemsLog;
    }

    /**
     * For debug
     * @return mixed
     */
    public function printSuggestedRelatedItemsDebug($relation)
    {
        echo '<pre>';
        echo "relatedItems:\n\n";
        print_r(\neam\util\U::arAttributes($this->$relation));
        echo "suggestedRelatedItems($relation):\n\n";
        print_r(\neam\util\U::arAttributes($this->suggestedRelatedItems($relation)));
        echo "suggestedUpdatedRelatedItems($relation):\n\n";
        print_r(\neam\util\U::arAttributes($this->suggestedUpdatedRelatedItems($relation)));
        echo '</pre>';
    }

}