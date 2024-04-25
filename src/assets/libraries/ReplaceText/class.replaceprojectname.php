<?php



/**
 * This Class is used to replace ProjectName in entire database.
 */
class ReplaceProjectName
{
    public function __construct()
    {
        global $obj;
        $this->replaceStr = 'ProjectName';
    }

    public static function getInstance()
    {
        return new self();
    }

    public function replace($keywords, $ignore_db_tables = []): void
    {
        global $obj;
        $all_tables = $this->getAllDBTables();
        // Loop all keywords
        foreach ($keywords as $searchStr) {
            $all_search_results = [];
            // Loop all tables
            foreach ($all_tables as $table) {
                if (in_array($table, $ignore_db_tables, true)) {
                    continue;
                }

                // Get all table columns
                $sql = "SELECT GROUP_CONCAT(`COLUMN_NAME`) as TABLE_COLUMNS FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='{$table}';";
                $results = $obj->MySQLSelect($sql);
                $TABLE_COLUMNS = explode(',', $results[0]['TABLE_COLUMNS']);

                // Create search query, get results
                $column1 = $TABLE_COLUMNS[0];
                $column_str = " (CONVERT (`{$column1}` USING utf8) LIKE '%{$searchStr}%'";
                $c = 1;
                foreach ($TABLE_COLUMNS as $column) {
                    if ($c > 1) {
                        $column_str .= " OR CONVERT(`{$column}` USING utf8) LIKE '%{$searchStr}%'";
                    }
                    ++$c;
                }
                $column_str .= ')';

                $sql1 = "SELECT * FROM {$table} WHERE ".$column_str;
                $search_results = $obj->MySQLSelect($sql1);

                if (!empty($search_results) && count($search_results) > 0) {
                    $primary_key_data = $obj->MySQLSelect("SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'");
                    $primary_key = $primary_key_data[0]['Column_name'];

                    foreach ($search_results as $rows) {
                        foreach ($rows as $key => $value) {
                            if (preg_match('/\\b'.$searchStr.'\\b/i', $value)) {
                                $all_search_results[$table][$primary_key][$rows[$primary_key]][] = $key;
                            }
                        }
                    }

                    if (isset($all_search_results[$table][$primary_key])) {
                        $table_results = $all_search_results[$table][$primary_key];
                        $table_primary_key_ids = array_keys($table_results);
                        $table_primary_key_ids = implode(',', $table_primary_key_ids);

                        $table_primary_key_results = array_values($table_results);
                        $table_primary_key_results_all = [];
                        foreach ($table_primary_key_results as $key => $value) {
                            if (is_array($value)) {
                                $table_primary_key_results_all = array_merge($table_primary_key_results_all, $value);
                            }
                        }
                        $table_primary_key_results_all = array_unique($table_primary_key_results_all);
                        $table_primary_key_results_all = implode(',', $table_primary_key_results_all);

                        $all_search_results[$table][$primary_key] = ['primary_key_ids' => $table_primary_key_ids, 'table_fields' => $table_primary_key_results_all];
                    }
                }
                // Create search query and get results end
            }

            // Replace and update keywords with "ProjectName"
            foreach ($all_search_results as $key => $value) {
                $primary_key_field = array_keys($value)[0];
                $table_fields = $value[$primary_key_field]['table_fields'];
                $table_ids = $value[$primary_key_field]['primary_key_ids'];
                $table_fields_arr = explode(',', $table_fields);

                $sql = "SELECT {$primary_key_field},{$table_fields} FROM {$key} WHERE {$primary_key_field} IN ({$table_ids})";
                $res = $obj->MySQLSelect($sql);

                if (!empty($res) && count($res)) {
                    foreach ($res as $row) {
                        $sql_update = "UPDATE {$key} SET ";
                        $update_data = [];
                        $sql_update_str = '';
                        foreach ($table_fields_arr as $field) {
                            $update_data = $this->replaceProjectName($row[$field], $searchStr, $this->replaceStr);
                            $sql_update_str .= " {$field} = '".$obj->SqlEscapeString($update_data)."', ";
                        }

                        $sql_update_str = rtrim($sql_update_str, ', ');
                        $sql_update .= $sql_update_str." WHERE {$primary_key_field} = ".$row[$primary_key_field];

                        echo $sql_update.'<br><br>';

                        // $obj->sql_query($sql_update);
                    }
                }
            }
            // Replace and update keywords with "ProjectName"
        }
    }

    private function getAllDBTables()
    {
        global $obj;
        $allDbTables = $obj->MySQLSelect("SELECT DISTINCT(TABLE_NAME) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME");

        $tablesArr = [];
        foreach ($allDbTables as $value) {
            $tablesArr[] = $value['TABLE_NAME'];
        }

        return $tablesArr;
    }

    private function replaceProjectName($string, $searchStr, $replaceStr)
    {
        return preg_replace('/\\b'.$searchStr.'\\b/i', $replaceStr, $string);
    }
}
