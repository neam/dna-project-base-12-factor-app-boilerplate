<?php

/**
 * Contains global shorthands for commonly used snippets of code
 *
 * Class AppUtil
 */
class AppUtil
{

    static function prefixed_table_fields_wildcard($table, $alias, $field_names)
    {
        $prefixed = array();
        foreach ($field_names as $field_name) {
            $prefixed[] = "`{$alias}`.`{$field_name}` AS `{$alias}.{$field_name}`";
        }
        return implode(", ", $prefixed);
    }

    static function arAttributes($ars)
    {
        $return = array();
        foreach ($ars as $ar) {
            $return[] = $ar->attributes;
        }
        return $return;
    }

    /**
     * U::inspection(__METHOD__, func_get_args());
     * @param string $method
     * @param mixed $args
     */
    static public function inspection($method, $args)
    {
        Yii::log($method . " params: " . print_r($args, true), "inspection", $method);
    }

    /** @return bool */
    static public function isInConsoleMode()
    {
        return Yii::app() instanceof CConsoleApplication || PHP_SAPI == 'cli';
    }

    /**
     * Adaption of https://github.com/yiisoft/yii/blob/1.1.16/framework/db/schema/CDbCriteria.php#L414
     *
     * @param $column
     * @param $value
     * @param bool $partialMatch
     * @param string $operator
     * @param bool $escape
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria $query
     */
    static public function compare(
        \Propel\Runtime\ActiveQuery\ModelCriteria &$query,
        $column,
        $value,
        $partialMatch = false,
        $operator = 'AND',
        $escape = true,
        $caseInsensitive = true
    ) {
        if (is_array($value)) {
            if ($value === array()) {
                return $query;
            }
            throw new Exception("TODO");
            return $query->addInCondition($column, $value, $operator);
        } else {
            $value = "$value";
        }

        if (preg_match('/^(?:\s*(<>|<=|>=|<|>|=))?(.*)$/', $value, $matches)) {
            $value = $matches[2];
            $op = $matches[1];
        } else {
            $op = '';
        }

        if ($value === '') {
            return $query;
        }

        if ($partialMatch) {
            if ($op === '') {
                return static::addSearchCondition($query, $column, $value, $escape, $operator);
            }
            if ($op === '<>') {
                return static::addSearchCondition($query, $column, $value, $escape, $operator, 'NOT LIKE');
            }
        } elseif ($op === '') {
            $op = '=';
        }

        if ($caseInsensitive && !ctype_digit($value)) {
            $query->where("LOWER($column)" . $op . '?', mb_strtolower($value));
        } else {
            $query->where($column . $op . '?', $value);
        }

        if (strtolower($operator) == 'or') {
            $query->_or();
        }

        return $query;
    }

    const PARAM_PREFIX = ':ycp';

    static public function addSearchCondition(
        \Propel\Runtime\ActiveQuery\ModelCriteria &$query,
        $column,
        $keyword,
        $escape = true,
        $operator = 'AND',
        $like = 'LIKE',
        $caseInsensitive = true
    ) {
        if ($keyword == '') {
            return $query;
        }
        if ($escape) {
            $keyword = '%' . strtr($keyword, array('%' => '\%', '_' => '\_', '\\' => '\\\\')) . '%';
        }

        if ($caseInsensitive) {
            $condition = "LOWER($column)" . " $like ?";
            $query->where($condition, mb_strtolower($keyword));
        } else {
            $condition = $column . " $like ?";
            $query->where($condition, $keyword);
        }

        if (strtolower($operator) == 'or') {
            $query->_or();
        }

        return $query;

    }

    static public function propelQuerySQL(
        \Propel\Runtime\ActiveQuery\ModelCriteria $query
    ) {

        $placeholderParams = [];
        $query = clone $query;
        $sql = static::createSelectSqlAndSetCorrespondingPlaceholderParams($query, $placeholderParams);
        // array_reverse so that longer placeholders gets replaced before the shorter ones. otherwise ":p10" gets replaced by ":p1" param...
        foreach (array_reverse($placeholderParams) as $key => $value) {
            if ($value instanceof DateTime) {
                $value = $value->format("Y-m-d H:i:s");
            }
            $sql = str_replace(":$key", "'$value'", $sql);
        }
        $sql = str_replace("SELECT  FROM", "SELECT * FROM", $sql);
        return trim($sql);

    }

    static public function createSelectSqlAndSetCorrespondingPlaceholderParams(
        \Propel\Runtime\ActiveQuery\ModelCriteria $query,
        &$placeholderParams
    ) {

        return static::createSqlAndSetCorrespondingPlaceholderParams($query, "createSelectSql", $placeholderParams);

    }

    static public function propelCountSQL(
        \Propel\Runtime\ActiveQuery\ModelCriteria $query
    ) {

        $placeholderParams = [];
        $query = clone $query;
        $sql = static::createCountSqlAndSetCorrespondingPlaceholderParams($query, $placeholderParams);
        foreach ($placeholderParams as $key => $value) {
            if ($value instanceof DateTime) {
                $value = $value->format("Y-m-d H:i:s");
            }
            $sql = str_replace(":$key", "'$value'", $sql);
        }
        return trim($sql);

    }

    static public function createCountSqlAndSetCorrespondingPlaceholderParams(
        \Propel\Runtime\ActiveQuery\ModelCriteria $query,
        &$placeholderParams
    ) {

        return static::createSqlAndSetCorrespondingPlaceholderParams($query, "createCountSql", $placeholderParams);

    }

    static public function createSqlAndSetCorrespondingPlaceholderParams(
        \Propel\Runtime\ActiveQuery\ModelCriteria $query,
        $method,
        &$placeholderParams
    ) {

        $params = []; // This will be filled with the parameters
        $sql = $query->$method($params);
        $index = 1;
        foreach ($params as $param) {
            $placeholderParams["p" . $index] = $param["value"];
            $index++;
        }
        return $sql;

    }

}