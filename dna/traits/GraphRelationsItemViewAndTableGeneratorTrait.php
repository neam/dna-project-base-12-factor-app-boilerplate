<?php

trait GraphRelationsItemViewAndTableGeneratorTrait
{

    /**
     * item
     *      node_id
     *      id
     *      _title
     *      status
     *      draft_validation_progress
     *      reviewable_validation_progress
     *      publishable_validation_progress
     *      approval_progress
     *      proofing_progress
     *      translate_into_{$language}_validation_progress
     *      model_class
     *      item_type
     *      created
     *      modified
     */
    public function actionItem($verbose = false)
    {

        if (!empty($verbose)) {
            $this->_verbose = true;
        }

        // We require node_id and qa state for the item types to be available via this view
        $qaModels = array_intersect_assoc(ItemTypes::where('is_graph_relatable'), ItemTypes::where('is_preparable'));

        $this->_db =& Yii::app()->{$this->connectionID};

        $this->d("Connecting to '" . $this->_db->connectionString . "'\n");

        $sql = "SELECT \n";

        // node_id
        $sql .= "   node.id as node_id,\n";

        // id
        $sql .= "   CASE\n";
        foreach ($qaModels as $modelClass => $table) {
            if ($this->_checkTableAndColumnExists($table, "id")) {
                $sql .= "       WHEN `$table`.id IS NOT NULL THEN `$table`.id\n";
            }
        }
        $sql .= "       ELSE NULL\n";
        $sql .= "END AS id,\n";

        // _title - TODO: Move all items to use heading instead of title
        $sql .= "   CASE\n";

        foreach ($qaModels as $modelClass => $table) {
            if ($this->_checkTableAndColumnExists($table, "_title")) {
                $sql .= "       WHEN `$table`.id IS NOT NULL THEN `$table`._title\n";
            }
            if ($this->_checkTableAndColumnExists($table, "_heading")) {
                $sql .= "       WHEN `$table`.id IS NOT NULL THEN `$table`._heading\n";
            }
        }

        $sql .= "       ELSE NULL\n";
        $sql .= "END AS _title,\n";

        // status
        $sql .= "   CASE\n";

        foreach ($qaModels as $modelClass => $table) {
            if ($this->_checkTableAndColumnExists($table . "_qa_state", "status")) {
                $sql .= "       WHEN `{$table}_qa_state`.status IS NOT NULL THEN `{$table}_qa_state`.status\n";
            }
        }

        $sql .= "       ELSE NULL\n";
        $sql .= "END AS status,\n";

        // draft_validation_progress
        $sql .= "   CASE\n";

        foreach ($qaModels as $modelClass => $table) {
            if ($this->_checkTableAndColumnExists($table . "_qa_state", "draft_validation_progress")) {
                $sql .= "       WHEN `{$table}_qa_state`.draft_validation_progress IS NOT NULL THEN `{$table}_qa_state`.draft_validation_progress\n";
            }
        }

        $sql .= "       ELSE NULL\n";
        $sql .= "END AS draft_validation_progress,\n";

        // reviewable_validation_progress
        $sql .= "   CASE\n";

        foreach ($qaModels as $modelClass => $table) {
            if ($this->_checkTableAndColumnExists($table . "_qa_state", "reviewable_validation_progress")) {
                $sql .= "       WHEN `{$table}_qa_state`.reviewable_validation_progress IS NOT NULL THEN `{$table}_qa_state`.reviewable_validation_progress\n";
            }
        }

        $sql .= "       ELSE NULL\n";
        $sql .= "END AS reviewable_validation_progress,\n";

        // publishable_validation_progress
        $sql .= "   CASE\n";

        foreach ($qaModels as $modelClass => $table) {
            if ($this->_checkTableAndColumnExists($table . "_qa_state", "publishable_validation_progress")) {
                $sql .= "       WHEN `{$table}_qa_state`.publishable_validation_progress IS NOT NULL THEN `{$table}_qa_state`.publishable_validation_progress\n";
            }
        }

        $sql .= "       ELSE NULL\n";
        $sql .= "END AS publishable_validation_progress,\n";

        // approval_progress
        $sql .= "   CASE\n";

        foreach ($qaModels as $modelClass => $table) {
            if ($this->_checkTableAndColumnExists($table . "_qa_state", "approval_progress")) {
                $sql .= "       WHEN `{$table}_qa_state`.approval_progress IS NOT NULL THEN `{$table}_qa_state`.approval_progress\n";
            }
        }

        $sql .= "       ELSE NULL\n";
        $sql .= "END AS approval_progress,\n";

        // proofing_progress
        $sql .= "   CASE\n";

        foreach ($qaModels as $modelClass => $table) {
            if ($this->_checkTableAndColumnExists($table . "_qa_state", "proofing_progress")) {
                $sql .= "       WHEN `{$table}_qa_state`.proofing_progress IS NOT NULL THEN `{$table}_qa_state`.proofing_progress\n";
            }
        }

        $sql .= "       ELSE NULL\n";
        $sql .= "END AS proofing_progress,\n";

        // translate_into_{$language}_validation_progress
        foreach (LanguageHelper::getCodes() as $language) {

            $sql .= "   CASE\n";

            foreach ($qaModels as $modelClass => $table) {
                if ($this->_checkTableAndColumnExists(
                    $table . "_qa_state",
                    "translate_into_{$language}_validation_progress"
                )
                ) {
                    $sql .= "       WHEN `{$table}_qa_state`.translate_into_{$language}_validation_progress IS NOT NULL THEN `{$table}_qa_state`.translate_into_{$language}_validation_progress\n";
                }
            }

            $sql .= "       ELSE NULL\n";
            $sql .= "END AS translate_into_{$language}_validation_progress,\n";

        }

        // model_class
        $sql .= "   CASE\n";

        foreach ($qaModels as $modelClass => $table) {
            $sql .= "       WHEN `$table`.id IS NOT NULL THEN '$modelClass'\n";
        }

        $sql .= "       ELSE NULL\n";
        $sql .= "END AS model_class,\n";

        // item_type
        $sql .= "   CASE\n";

        foreach ($qaModels as $modelClass => $table) {
            $sql .= "       WHEN `$table`.id IS NOT NULL THEN 'item'\n";
        }

        $sql .= "       ELSE NULL\n";
        $sql .= "END AS item_type,\n";

        // created
        $sql .= "   CASE\n";

        foreach ($qaModels as $modelClass => $table) {
            if ($this->_checkTableAndColumnExists($table, "created")) {
                $sql .= "       WHEN `$table`.created IS NOT NULL THEN $table.created\n";
            }
        }

        $sql .= "       ELSE NULL\n";
        $sql .= "END AS created,\n";

        // created
        $sql .= "   CASE\n";

        foreach ($qaModels as $modelClass => $table) {
            if ($this->_checkTableAndColumnExists($table, "modified")) {
                $sql .= "       WHEN `$table`.modified IS NOT NULL THEN `$table`.modified\n";
            }
        }

        $sql .= "       ELSE NULL\n";
        $sql .= "END AS modified\n";

        $sql .= "FROM node\n";

        foreach ($qaModels as $modelClass => $table) {
            $sql .= "       LEFT JOIN (`$table` INNER JOIN `{$table}_qa_state` ON `{$table}`.{$table}_qa_state_id = `{$table}_qa_state`.id) ON `$table`.node_id = node.id \n";
        }

        $viewName = "item";

        $viewSql = "CREATE OR REPLACE VIEW item AS $sql";

        if ($this->_verbose) {
            echo "\n";
            echo $viewSql;
            echo "\n";
        }

        $selectResult = $this->_db->createCommand($sql . " LIMIT 2")->queryAll();
        $this->_db->getPdoInstance()->exec($viewSql);
        $selectViewResult = $this->_db->createCommand("SELECT * FROM $viewName LIMIT 2")->queryAll();

        if ($this->_verbose) {
            var_dump(compact("selectResult", "selectViewResult"));
        }

        $selectExistingItemsResult = $this->_db->createCommand(
            "SELECT * FROM $viewName WHERE model_class IS NOT NULL LIMIT 2"
        )->queryAll();

        if ($this->_verbose) {
            var_dump(compact("selectExistingItemsResult"));
        }

    }

    public function actionItemTable($verbose = false)
    {

        if (!empty($verbose)) {
            $this->_verbose = true;
        }

        $this->_db =& Yii::app()->{$this->connectionID};

        $this->d("Connecting to '" . $this->_db->connectionString . "'\n");

        // TODO: Generate this sql
        $this->_db->createCommand("DROP TABLE IF EXISTS `_item`;")->execute();
        $sql = "CREATE TABLE `_item` (
  `node_id` bigint(20) NOT NULL,
  `id` bigint(20) NOT NULL,
#  `version` int(11) NOT NULL DEFAULT '1',
#  `cloned_from_id` bigint(20) DEFAULT NULL,
  `_title` varchar(255) COLLATE utf8_bin DEFAULT NULL,
#  `slug_en` varchar(255) COLLATE utf8_bin DEFAULT NULL,
#  `thumbnail_media_id` int(11) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `draft_validation_progress` int(11) DEFAULT NULL,
  `reviewable_validation_progress` int(11) DEFAULT NULL,
  `publishable_validation_progress` int(11) DEFAULT NULL,
  `approval_progress` int(11) DEFAULT NULL,
  `proofing_progress` int(11) DEFAULT NULL,
  `translate_into_en_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ar_validation_progress` int(11) DEFAULT NULL,
  `translate_into_bg_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ca_validation_progress` int(11) DEFAULT NULL,
  `translate_into_cs_validation_progress` int(11) DEFAULT NULL,
  `translate_into_da_validation_progress` int(11) DEFAULT NULL,
  `translate_into_de_validation_progress` int(11) DEFAULT NULL,
  `translate_into_en_gb_validation_progress` int(11) DEFAULT NULL,
  `translate_into_en_us_validation_progress` int(11) DEFAULT NULL,
  `translate_into_el_validation_progress` int(11) DEFAULT NULL,
  `translate_into_es_validation_progress` int(11) DEFAULT NULL,
  `translate_into_fa_validation_progress` int(11) DEFAULT NULL,
  `translate_into_fi_validation_progress` int(11) DEFAULT NULL,
  `translate_into_fil_validation_progress` int(11) DEFAULT NULL,
  `translate_into_fr_validation_progress` int(11) DEFAULT NULL,
  `translate_into_he_validation_progress` int(11) DEFAULT NULL,
  `translate_into_hi_validation_progress` int(11) DEFAULT NULL,
  `translate_into_hr_validation_progress` int(11) DEFAULT NULL,
  `translate_into_hu_validation_progress` int(11) DEFAULT NULL,
  `translate_into_id_validation_progress` int(11) DEFAULT NULL,
  `translate_into_it_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ja_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ko_validation_progress` int(11) DEFAULT NULL,
  `translate_into_lt_validation_progress` int(11) DEFAULT NULL,
  `translate_into_lv_validation_progress` int(11) DEFAULT NULL,
  `translate_into_nl_validation_progress` int(11) DEFAULT NULL,
  `translate_into_no_validation_progress` int(11) DEFAULT NULL,
  `translate_into_pl_validation_progress` int(11) DEFAULT NULL,
  `translate_into_pt_validation_progress` int(11) DEFAULT NULL,
  `translate_into_pt_br_validation_progress` int(11) DEFAULT NULL,
  `translate_into_pt_pt_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ro_validation_progress` int(11) DEFAULT NULL,
  `translate_into_ru_validation_progress` int(11) DEFAULT NULL,
  `translate_into_sk_validation_progress` int(11) DEFAULT NULL,
  `translate_into_sl_validation_progress` int(11) DEFAULT NULL,
  `translate_into_sr_validation_progress` int(11) DEFAULT NULL,
  `translate_into_sv_validation_progress` int(11) DEFAULT NULL,
  `translate_into_th_validation_progress` int(11) DEFAULT NULL,
  `translate_into_tr_validation_progress` int(11) DEFAULT NULL,
  `translate_into_uk_validation_progress` int(11) DEFAULT NULL,
  `translate_into_vi_validation_progress` int(11) DEFAULT NULL,
  `translate_into_zh_validation_progress` int(11) DEFAULT NULL,
  `translate_into_zh_cn_validation_progress` int(11) DEFAULT NULL,
  `translate_into_zh_tw_validation_progress` int(11) DEFAULT NULL,
  `model_class` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `item_type` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";

        $this->_db->createCommand($sql)->execute();

    }

    /**
     * @param $model
     * @param $column
     * @return bool
     */
    protected function _checkTableAndColumnExists($table, $column)
    {
        $tables = $this->_db->schema->getTables();
        // The column does not exist if the table does not exist
        return isset($tables[$table]) && (isset($tables[$table]->columns[$column]));
    }

}