<?php

use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;

/**
 * This Class is used to perform actions on cab_request_now table.
 */
class RequestNow
{
    public const OP_INSERT = 1;
    public const OP_UPDATE = 2;
    public const OP_SELECT = 3;
    public const TB_NAME = 'cab_request_now';
    public const ORD_ASC = 1;
    public const ORD_DESC = 2;

    private $dbFieldsArr = [];
    private $currentOperation;
    private $currentOrderBy;
    private $currentOrderByOP;
    private $currentStartLimit;
    private $currentEndLimit;
    private $conditionsArr = [];

    public function __construct($dbFieldsArr = [])
    {
        $this->dbFieldsArr = $dbFieldsArr;
    }

    public static function getInstance($dbFieldsArr)
    {
        return new self($dbFieldsArr);
    }

    public function validateData(): void
    {
        if (empty($this->currentOperation)) {
            echo 'Data Operation is not passed.';
            echo '<BR/><BR/>This must be one of below values:<BR/><BR/>';
            echo '1) RequestNow::OP_INSERT <BR/>2) RequestNow::OP_UPDATE <BR/>3) RequestNow::OP_SELECT';

            exit;
        }

        if (self::OP_INSERT === $this->currentOperation || self::OP_UPDATE === $this->currentOperation) {
            if (empty($this->dbFieldsArr) || 0 === count($this->dbFieldsArr)) {
                echo 'Data array must not be blank.';

                exit;
            }
            if ((array_keys($this->dbFieldsArr) !== range(0, count($this->dbFieldsArr) - 1)) === false) {
                echo 'Array must be an Associative for insert OR update operation.';

                exit;
            }
        } else {
            if (!empty($this->dbFieldsArr) && count($this->dbFieldsArr) > 0 && array_keys($this->dbFieldsArr) !== range(0, count($this->dbFieldsArr) - 1)) {
                echo 'Array must be an Sequential for select operation.';

                exit;
            }
        }
    }

    public function execute($operation, $conditionsArr = [], $orderBy = '', $orderByOP = '', $limit_start = '', $limit_start_end = '')
    {
        $this->currentOperation = $operation;
        $this->conditionsArr = $conditionsArr;
        $this->currentOrderBy = $orderBy;
        $this->currentOrderByOP = $orderByOP;
        $this->currentStartLimit = $limit_start;
        $this->currentEndLimit = $limit_start_end;
        $this->validateData();

        if ($MODULES_OBJ->isMongoDBAvailable()) {
            return $this->executeProcessFromMongoDB();
        }

        return $this->executeProcessFromMySQL();
    }

    public function getProperDataValue($text)
    {
        global $obj;
        if ('STRING' !== strtoupper(gettype($text))) {
            return $text;
        }

        return $this->clean(htmlspecialchars_decode(html_entity_decode(stripcslashes($text)), ENT_QUOTES));
    }

    public function clean($str)
    {
        global $obj;

        if (!is_array($str)) {
            $str = trim($str);
        }

        return str_replace("'", "\\'", $str);
        // return ($str);
    }

    private function isInsertOp()
    {
        return self::OP_INSERT === $this->currentOperation;
    }

    private function isUpdateOp()
    {
        return self::OP_UPDATE === $this->currentOperation;
    }

    private function isSelectOp()
    {
        return self::OP_SELECT === $this->currentOperation;
    }

    private function retrieveSelectionFromArr($result_res)
    {
        // echo "<PRE>";
        // print_r($result_res);exit;

        $final_results_arr = [];

        $isFieldSelectionEnable = (empty($this->dbFieldsArr) || 0 === count($this->dbFieldsArr)) ? false : true;

        for ($i = 0; $i < count($result_res); ++$i) {
            $result_res_item = $result_res[$i];

            $result_res_item['iCabRequestId'] = $result_res_item['_id']['$oid'];

            $results_arr = [];
            if (!$isFieldSelectionEnable) {
                $results_arr = array_merge($results_arr, $result_res_item);
            } else {
                foreach ($this->dbFieldsArr as $item) {
                    $item = trim($item);

                    $item_arr = preg_split('/\\bas\\b/i', $item);
                    // print_r($item_arr);exit;

                    if (count($item_arr) > 1) {
                        $item_arr[0] = trim($item_arr[0]);
                        $item_arr[1] = trim($item_arr[1]);
                        if (isset($result_res_item[$item_arr[0]])) {
                            $results_arr[$item_arr[1]] = $result_res_item[$item_arr[0]];
                        }
                    } else {
                        if (isset($result_res_item[$item])) {
                            $results_arr[$item] = $result_res_item[$item];
                        }
                    }
                }
            }

            $final_results_arr[] = $results_arr;
        }

        return $final_results_arr;
    }

    private function getOrderOPForMySQL()
    {
        if (!empty($this->currentOrderByOP)) {
            return self::ORD_ASC === $this->currentOrderByOP ? 'ASC' : 'DESC';
        }

        return '';
    }

    private function executeProcessFromMongoDB()
    {
        global $obj, $mecachedClsObj;

        $dataArr = [];
        $dataArr['TABLE_NAME'] = self::TB_NAME;
        // $dataArr['SEARCH_PARAMS'] = $this->conditionsArr;

        foreach ($dataArr['FILTER_PARAMS'] as $keyOfItem => $valueOfData) {
            if (str_contains($valueOfData, ',')) {
                $valueOfData_arr = explode(',', preg_replace('/\s+/', '', $valueOfData));

                $dataArr_filter_param[$keyOfItem]['$'.'in'] = $valueOfData_arr;
            } else {
                $dataArr_filter_param[$keyOfItem] = $valueOfData;
            }
        }

        $dataArr['FILTER_PARAMS'] = $dataArr_filter_param;

        if (!empty($this->conditionsArr) && count($this->conditionsArr) > 0) {
            $conditionsArr_tmp = [];
            foreach ($this->conditionsArr as $key => $value) {
                $value = trim($value);
                if (str_contains($value, ',')) {
                    $valueOfData_arr = explode(',', preg_replace('/\s+/', '', $value));
                    if ('iCabRequestId' === $key) {
                        for ($ij = 0; $ij < count($valueOfData_arr); ++$ij) {
                            $valueOfData_arr[$ij] = new ObjectID(trim($valueOfData_arr[$ij]));
                        }
                        $conditionsArr_tmp['_id']['$'.'in'] = $valueOfData_arr;
                    } else {
                        $conditionsArr_tmp[$key]['$'.'in'] = $valueOfData_arr;
                    }
                } else {
                    if ('iCabRequestId' === $key) {
                        $conditionsArr_tmp['_id'] = new ObjectID(trim($value));
                    } else {
                        $conditionsArr_tmp[$key] = $value;
                    }
                }
            }
            $this->conditionsArr = $conditionsArr_tmp;
        }

        $dataArr['FILTER_PARAMS'] = $this->conditionsArr;
        $dataArr['DATASET'] = $this->dbFieldsArr;

        // print_r($dataArr);exit;
        $records_res = [];

        if ($this->isSelectOp()) {
            if ((!empty($this->currentStartLimit) || '0' === $this->currentStartLimit || 0 === $this->currentStartLimit) && !empty($this->currentEndLimit)) {
                $dataArr['DATASET_LIMIT'] = (int) $this->currentEndLimit;
            }

            if (!empty($this->currentOrderBy)) {
                if ('iCabRequestId' === $this->currentOrderBy) {
                    $this->currentOrderBy = '_id';
                }

                $dataArr['SORT_PARAMS'] = [$this->currentOrderBy => 'ASC' === $this->getOrderOPForMySQL() ? 1 : -1];
            }

            $records_res = $this->retrieveSelectionFromArr($obj->fetchRecordsFromMongo($dataArr));
        } elseif ($this->isUpdateOp()) {
            $records_res = $obj->updateRecordsToMongo($dataArr);
        } elseif ($this->isInsertOp()) {
            // Get All Columns/Default Values of table.

            $isMemcachedAvailable = $MODULES_OBJ->isMemcachedAvailable();

            if ($isMemcachedAvailable) {
                $columns_data = $mecachedClsObj->retrieveData(self::TB_NAME.'_sql_columns_data');
            }

            if (empty($columns_data)) {
                $columns_data = $obj->MySQLSelect('SHOW COLUMNS FROM '.self::TB_NAME);
            }

            if ($isMemcachedAvailable) {
                $mecachedClsObj->setData(self::TB_NAME.'_sql_columns_data', $columns_data);
            }

            $default_columns_data_arr = [];
            foreach ($columns_data as $columns_data_item) {
                // if(isset($columns_data_item['Default']) && $columns_data_item['Default'] != ""){
                if ('CURRENT_TIMESTAMP' === $columns_data_item['Default']) {
                    $columns_data_item['Default'] = @date('Y-m-d H:i:s');
                    $default_columns_data_arr[$columns_data_item['Field'].'_clone'] = new UTCDateTime((new DateTime(@date('Y-m-d H:i:s')))->getTimestamp() * 1_000);
                }
                $default_columns_data_arr[$columns_data_item['Field']] = null === $columns_data_item['Default'] ? '' : $columns_data_item['Default'];
                // }
            }

            $dataArr['DATASET'] = $this->dbFieldsArr;

            $new_array = array_diff_key($default_columns_data_arr, $dataArr['DATASET']);

            $dataArr['DATASET'] = array_merge(array_diff_key($default_columns_data_arr, $dataArr['DATASET']), $dataArr['DATASET']);

            $records_res = $obj->insertRecordsToMongo($dataArr);
        }

        return $records_res;
    }

    private function executeProcessFromMySQL()
    {
        global $obj;

        if ($this->isSelectOp()) {
            $fields_arr = (empty($this->dbFieldsArr) || 0 === count($this->dbFieldsArr)) ? ' * ' : implode(',', $this->dbFieldsArr);

            $sql_tb_select = 'SELECT '.$fields_arr.' FROM `'.self::TB_NAME.'` ';

            if (!empty($this->conditionsArr) && count($this->conditionsArr) > 0) {
                $whereData = ' WHERE '.implode(' AND ', array_map(
                    static function ($v, $k) {
                        if (str_contains($v, ',')) {
                            $v_arr = explode(',', preg_replace('/\s+/', '', $v));
                            $v = "'".implode("','", $v_arr)."'";
                        }

                        // return sprintf("`%s`='%s'", $k, $v);
                        return sprintf("`%s` IN ('%s')", $k, $v);
                    },
                    $this->conditionsArr,
                    array_keys($this->conditionsArr)
                ));

                $sql_tb_select .= $whereData;
            }

            if (!empty($this->currentOrderBy)) {
                $sql_tb_select .= ' ORDER BY '.$this->currentOrderBy.' ';
                if (!empty($this->getOrderOPForMySQL())) {
                    $sql_tb_select .= ' '.$this->getOrderOPForMySQL().' ';
                }
            }

            if ((!empty($this->currentStartLimit) || '0' === $this->currentStartLimit || 0 === $this->currentStartLimit) && !empty($this->currentEndLimit)) {
                $sql_tb_select .= ' LIMIT '.$this->currentStartLimit.','.$this->currentEndLimit;
            }

            return $obj->MySQLSelect($sql_tb_select);
        }
        if ($this->isUpdateOp()) {
            $sql_tb_update = 'UPDATE `'.self::TB_NAME.'` SET '.implode(', ', array_map(
                static function ($v, $k) {
                    if (str_contains($v, ',')) {
                        $v_arr = explode(',', preg_replace('/\s+/', '', $v));
                        $v = "'".implode("','", $v_arr)."'";
                    }

                    // return sprintf("`%s`='%s'", $k, $v);
                    return sprintf("`%s` = '%s'", $k, $v);
                },
                $this->dbFieldsArr,
                array_keys($this->dbFieldsArr)
            ));

            if (!empty($this->conditionsArr) && count($this->conditionsArr) > 0) {
                $whereData = ' WHERE '.implode(' AND ', array_map(
                    static function ($v, $k) {
                        if (str_contains($v, ',')) {
                            $v_arr = explode(',', preg_replace('/\s+/', '', $v));
                            $v = "'".implode("','", $v_arr)."'";
                        }

                        // return sprintf("`%s`='%s'", $k, $v);
                        return sprintf("`%s` IN ('%s')", $k, $v);
                    },
                    $this->conditionsArr,
                    array_keys($this->conditionsArr)
                ));

                $sql_tb_update .= $whereData;
            }

            return $obj->sql_query($sql_tb_update);
        }
        if ($this->isInsertOp()) {
            $sql_tb_insert = 'INSERT INTO `'.self::TB_NAME.'` ('.implode(', ', array_map(
                static function ($v, $k) {
                    if (str_contains($v, ',')) {
                        $v_arr = explode(',', preg_replace('/\s+/', '', $v));
                        $v = "'".implode("','", $v_arr)."'";
                    }

                    // return sprintf("`%s`='%s'", $k, $v);
                    return sprintf('`%s`', $k);
                },
                $this->dbFieldsArr,
                array_keys($this->dbFieldsArr)
            )).') VALUES ('.implode(', ', array_map(
                function ($v, $k) {
                    /* if($v != "" && strpos($v, ",") !== false){
                        $v_arr = explode(",",preg_replace('/\s+/', '', $v));;
                        $v = "'".implode("','",$v_arr)."'";
                    } */
                    // return sprintf("`%s`='%s'", $k, $v);
                    if (null === $v) {
                        $v = '';
                    }

                    return sprintf("'%s'", $this->getProperDataValue($v));
                },
                $this->dbFieldsArr,
                array_keys($this->dbFieldsArr)
            )).') ';

            return $obj->sql_query($sql_tb_insert);
        }
    }
}
