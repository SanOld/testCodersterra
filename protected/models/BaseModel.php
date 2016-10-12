<?php

define ( 'ACTION_SELECT', 1 );
define ( 'ACTION_UPDATE', 2 );
define ( 'ACTION_INSERT', 3 );
define ( 'ACTION_DELETE', 4 );

class BaseModel extends CFormModel {
  public $table = '';
  public $id = false;
  public $user = array ();
  public $localDate = array ();
  public $method = false;
  public $isFilter = false;

  public $filePath = '';
  public $uploadPath = '';
  public $outerPath = '';

  // ------------ select ----------------
  protected function getCommand() {
    $command = Yii::app ()->db->createCommand ()->select ( '*' )->from ( $this->table );
    if ($this->id !== false) {
      $command->where ( 'id=:id', array (
          ':id' => $this->id
      ) );
    }
    return $command;
  }
  protected function setWhereByRole($command) {
    return $command;
  }
  protected function getCommandFilter() {
    $select = 'tbl.id, tbl.name';
    switch ($this->table){
      case 'spi_performer':
        $select.= ', tbl.short_name';
    };
    $command = Yii::app()->db->createCommand()->select ($select)
      ->from($this->table  . ' tbl');
    $command = $this->setWhereByRole($command);
    $command->order('name');
    return $command;
  }
  protected function getParamCommand($command, array $params) {
    $params = array_change_key_case ( $params, CASE_UPPER );
    if (safe($params, 'ID')) {
      $command->andWhere("tbl.id = :id", array(':id' => $params['ID']));
    }
    return $command;
  }

  protected function setLikeWhere($command, $keyword_fields, $value) {
    if(!$value) {
      return $command;
    }
    if(!is_array($keyword_fields)) {
      $keyword_fields = array($keyword_fields);
    }
    $where = array();
    $search_param = array();
    foreach($keyword_fields as $k=>$field) {
      $where[] = "{$field} like :key$k";
      $search_param["key$k"] = "%{$value}%";
    }
    if($where && $search_param) {
      $where = '(' . implode(' OR ', $where) . ')';
      $command -> andWhere($where, $search_param);
    }
    return $command;
  }

  protected function doSelect($command) {
    $commandClone = clone($command);
    $res = $command->queryAll();
    $result = array (
        'system_code' => 'SUCCESSFUL',
        'code' => '200'
    );
    if ($this->id !== false) {
      $result ['result'] = isset ( $res [0] ) ? $res [0] : array ();//TODO
    } else {
      $result ['result'] = $res;
      $result ['count'] = $this->getCountRes($commandClone);
    }
    return $result;
  }
  protected function getCountRes($command) {
    $command->select('COUNT(*) cnt')
      ->order('')
      ->limit('-1')
      ->offset('');
    $res = $command->queryAll();
    if(count($res) === 1) {
//      return print_r($res,1);
      return safe($res[0],'cnt',1);
    } else {
      return count($res);
    }
  }
  protected function doAfterSelect($result) {
    return $result;
  }

  // ------------ insert ----------------
  protected function doBeforeInsert($post) {
    return array (
        'result' => true,
        'params' => $post,
        'post' => $post
    );
  }
  protected function doInsert($params, $post, $table = false) {
    if(!$table) {
      $table = $this->table;
    }
    try{
      if (Yii::app ()->db->createCommand()->insert($table, $params)) {
        return array (
            'code' => '200',
            'result' => true,
            'id' => Yii::app()->db->getLastInsertID(),
            'system_code' => 'SUCCESSFUL'
        );
      } else {
        return array (
            'code' => '409',
            'result' => false,
            'system_code' => 'ERR_QUERY'
        );
      }
    } catch (CDbException $e) {
      return array (
          'code' => '409',
          'result' => false,
          'system_code' => 'ERR_QUERY',
          'db_message' => $e->errorInfo//TODO comment on production
      );
    }
  }
  protected function doAfterInsert($result, $params, $post) {
    return $result;
  }

  // ------------ update ----------------
  protected function doBeforeUpdate($post, $id) {
    return array (
        'result' => true,
        'params' => $post,
        'post' => $post
    );
  }
  protected function doUpdate($params, $post, $id) {
    try{
      if (Yii::app ()->db->createCommand ()->update ( $this->table, $params, 'id=:id', array (
          ':id' => $id
      ))>=0) {
        return array (
            'code' => '200',
            'result' => true,
            'system_code' => 'SUCCESSFUL'
        );
      } else {
        return array (
            'code' => '409',
            'result' => false,
            'system_code' => 'ERR_QUERY'
        );
      }
    } catch (CDbException $e) {
      return array (
          'code' => '409',
          'result' => false,
          'system_code' => 'ERR_QUERY',
          'db_message' => $e->errorInfo//TODO comment on production
      );
    }
  }

  protected function doAfterUpdate($result, $params, $post, $id) {
    return $result;
  }

  // ------------ delete ----------------
  protected function doBeforeDelete($id) {
    $row = Yii::app() -> db -> createCommand() -> select('id') -> from($this -> table . ' tbl') -> where('id=:id', array(
      ':id' => $id
    )) -> queryScalar();
    if (!$row) {
      return array(
        'code' => '409',
        'result' => false,
        'system_code' => 'ERR_NOT_EXISTS'
      );
    }

    return array(
      'result' => true
    );
  }
  protected function doDelete($id) {
    try{
      if (Yii::app ()->db->createCommand ()->delete ( $this->table, 'id=:id', array (
          ':id' => $id
      ) )) {
        return array (
            'code' => '200',
            'result' => true,
            'system_code' => 'SUCCESSFUL'
        );
      } else {
        return array (
            'code' => '409',
            'result' => false,
            'system_code' => 'ERR_QUERY'
        );
      }
    } catch (CDbException $e) {
      if ($e->getCode() == 23000){
        return $this->getForeignKeyError($e);
      } else {
        return array (
            'code' => '409',
            'result' => false,
            'system_code' => 'ERR_QUERY'
            );
      }
    }
  }
  protected function getForeignKeyError($e) {
    $table = strstr($e->errorInfo[2],'foreign key constraint fails (');
    $table = strstr($table,'`spi_');
    $table = explode('`', $table);
    $table = explode('spi_', $table[1]);
    return array('code' => '409', 'result'=> false, 'system_code'=> 'ERR_DEPENDENT_RECORD', 'table' => $table[1]);
  }
  protected function doAfterDelete($result, $id) {
    return $result;
  }
  protected function getRequired() {
    $query = "SELECT `COLUMN_NAME`
                  FROM `INFORMATION_SCHEMA`.`COLUMNS`
                 WHERE `TABLE_NAME`='" . $this->table . "'
                   AND `IS_NULLABLE`='NO'
                   AND `COLUMN_DEFAULT` IS NULL
                   AND `COLUMN_NAME` != 'id'";
    $required = Yii::app ()->db->createCommand ( $query )->queryAll ();
    $res = array ();
    foreach ( $required as $field ) {
      $res [] = $field ['COLUMN_NAME'];
    }
    return $res;
  }
  protected function getAllTableFields() {
    $query = "SELECT `COLUMN_NAME`, `DATA_TYPE`
                  FROM `INFORMATION_SCHEMA`.`COLUMNS`
                 WHERE `TABLE_NAME`='" . $this->table . "'";
    $required = Yii::app ()->db->createCommand ( $query )->queryAll ();
    $res = array ();
    foreach ( $required as $field ) {
      $res [] = array (
          'colname' => $field ['COLUMN_NAME'],
          'coltype' => $field ['DATA_TYPE']
      );
    }
    return $res;
  }
  protected function checkRequired($fields) {
    $required = $this->getRequired ();
    if ($this->method == 'put') {
      $row = Yii::app ()->db->createCommand ()->select ( '*' )->from ( $this->table )->where ( 'id=:id ', array (
          ':id' => $this->id
      ) )->queryRow ();

      foreach ( $row as $name => $field ) {
        $fields [$name] = isset ( $fields [$name] ) ? $fields [$name] : $field;
      }
    }
    $missed = array ();
    foreach ( $required as $field ) {
      if (! isset ( $fields [$field] ) || $fields [$field] === '') {
        $missed [] = $field;
      }
    }
    return $missed;
  }

  protected function addRelations($user, $command) {
    switch ($user['type']) {
      case TA :
        $command -> join('spi_performer pfm ', 'pfm.id=tbl.relation_id');
        break;
      case SCHOOL :
        $command -> join('spi_school scl ', 'scl.id=tbl.relation_id');
        break;
      case DISTRICT :
        $command -> join('spi_district dst ', 'dst.id=tbl.relation_id');
        break;
    }
    return $command;
  }

  protected function getRelationByType($type) {
    switch ($type) {
      case TA :
        return array(
          'name'   => 'Träger Agentur',
          'code'   => 'performer',
          'prefix' => 'pfm',
          'table'  => 'spi_performer',
        );
        break;
      case SCHOOL :
        return array(
          'name'   => 'Schule',
          'code'   => 'school',
          'prefix' => 'scl',
          'table'  => 'spi_school',
        );
        break;
      case DISTRICT :
        return array(
          'name'   => 'Bezirk',
          'code'   => 'district',
          'prefix' => 'dst',
          'table'  => 'spi_district',
        );
        break;
    }
    return array('name' => 'Keine Verbindung');
  }

  public function insert($post, $multiInsert = false) {
    $this->method = 'post';
    if ($this->checkPermission($this->user, ACTION_INSERT, $post)) {
      $results = $this->doBeforeInsert($post);
      if ($results ['result']) {
        $params = safe($results, 'params', $post);
        $missed = $this->checkRequired($params);
        if (!$missed) {
          $results = $this->doInsert($params, $post);
//          print_r($results);
          $results = $this->doAfterInsert($results, $params, $post);
          if ($multiInsert && $results['code'] == '200') {
            return $results;
          } else {
            response($results ['code'], $results, $this->method);
          }
        } else {
          response('400', array('result' => false, 'system_code' => 'ERR_MISSED_REQUIRED_PARAMETERS', 'required' => $missed), $this->method);
        }
      } else {
        if ($multiInsert && $results['code'] == '200') {
          return $results;
        } else {
          response($results ['code'], $results, $this->method);
        }
      }
    } else {
      response('403', array(
        'result' => false,
        'system_code' => 'ERR_PERMISSION'
      ));
    }
  }
  public function update($id, $post, $multiInsert = false) {
    $this->id = $id;
    $this->method = 'put';
    if ($this->checkPermission($this->user, ACTION_UPDATE, $post)) {
      if ($id !== false && $id !== NULL) {
        $result = $this->doBeforeUpdate($post, $id);
        if ($result['result']) {
          $params = safe($result, 'params', $post);
          $missed = $this->checkRequired($params);
          if (!$missed && !empty($params)) {
            $results = $this->doUpdate($params, $post, $id);
            $results = $this->doAfterUpdate($results, $params, $post, $id);
            if ($multiInsert && $results['code'] == '200') {
              return $results;
            } else {
              response($results['code'], $results, $this->method);
            }
          } else {
            response('400', array('result' => false, 'system_code' => 'ERR_MISSED_REQUIRED_PARAMETERS', 'required' => $missed), $this->method);
          }
        } else {
          if ($multiInsert && $result['code'] == '200') {
            return $result;
          } else {
            response($result['code'], $result, $this->method);
          }
        }
      } else {
        response('405', array('result' => false, 'system_code' => 'ERR_ID_NOT_SPECIFIED'), $this->method);
      }
    } else {
      response('403', array(
        'result' => false,
        'system_code' => 'ERR_PERMISSION'
      ));
    }
  }
  public function delete($id, $multiple = false) {
    $this->id = $id;
    $this->method = 'delete';
    if ($this->checkPermission($this->user, ACTION_DELETE, $id)) {
      if ($id !== false) {
        $result = $this->doBeforeDelete($id);
        if ($result ['result']) {
          $results = $this->doDelete($id);
          $results = $this->doAfterDelete($results, $id);
          if($multiple) {
            return $results;
          } else {
            response($results ['code'], $results, $this->method);
          }
        } else {
          if($multiple) {
            return $result;
          } else {
            response($result ['code'], $result, $this->method);
          }
        }
      } else {
        if($multiple) {
          return array('result' => false, 'system_code' => 'ERR_ID_NOT_SPECIFIED');
        } else {
          response('405', array('result' => false, 'system_code' => 'ERR_ID_NOT_SPECIFIED'), $this->method);
        }

      }
    } else {
      if($multiple) {
          return array(
            'result' => false,
            'system_code' => 'ERR_PERMISSION'
          );
        } else {
          response('403', array(
            'result' => false,
            'system_code' => 'ERR_PERMISSION'
          ));
        }

    }
  }
  function setPagination($command, $get) {
    if(safe($get, 'limit')) {
      $command->limit($get['limit']);
      if(safe($get, 'page') && $get['page'] > 1) {
        $command->offset(($get['page'] - 1) * $get['limit']);
      }
    }
    return $command;
  }

  function isExistsField($fieldName) {
    foreach($this->getAllTableFields() as $field) {
      if($field['colname'] == $fieldName) {
        return true;
      }
    }
    return false;
  }

  function setOrder($command, $get) {
    if(safe($get, 'order')) {
//      if(!$this->isExistsField($get['order'])) {
//        response ( '409', array (
//          'result' => false,
//          'system_code' => 'ERR_INVALID_FIELD_ORDER'
//        ), $this->method );
//      }
      $direction = safe($get, 'direction', 'ASC');
      $command->order($get['order'].' '.$direction);
    }
    return $command;
  }
  public function select($get) {
    $this->method = 'get';
    if($this->checkPermission($this->user, ACTION_SELECT, $get) || (get_called_class() == 'Hint' && $this->isFilter)) {
      $params = array_change_key_case($get, CASE_UPPER);
      if(safe($params,'GET_NEXT_ID')) {
        $results = $this->getNextId ();
        response ( $results ['code'], $results , $this->method);
      } else {
        if($this->isFilter && get_called_class() != 'Hint') {
          $command = $this->getCommandFilter();
        } else {
          $command = $this->getCommand();
        }
        if (!empty ($get)) {
          $command = $this->setPagination($command, $get);
          $command = $this->setOrder($command, $get);
          $command = $this->getParamCommand($command, $get, array());
        }
        if ($command) {
          $results = $this->doSelect($command);
          $results = $this->calcResults($results);
          $results = $this->doAfterSelect($results);

          response($results ['code'], $results, $this->method);
        } else {
          response('409', array('result' => false, 'system_code' => 'ERR_INVALID_QUERY'), $this->method);
        }
      }
    } else {
      response('403', array(
        'result' => false,
        'system_code' => 'ERR_PERMISSION'
      ));
    }
  }
  protected function getNextId() {
    $custom_codes = Yii::app()->db->createCommand('SELECT sct.id, UPPER(prj.real_code) AS real_code, MAX(prj.code) max_code, prj.is_manual, IF(pt.id="3",1,0) AS is_bonus FROM spi_project prj 
                                          JOIN spi_school_type sct ON prj.school_type_id=sct.id 
                                          JOIN spi_project_type pt ON prj.type_id=pt.id  
                                          WHERE prj.code NOT LIKE "%\\\\\\\\%"
                                          AND prj.is_manual = 0                                           
                                          AND prj.real_code IS NOT NULL    
                                          GROUP BY prj.real_code, is_bonus')->queryAll();
    $pattern_number = "/[0-9]+$/";
    $pattern = "/^[a-zA-Z]{1,2}/"; 
    
    foreach ($custom_codes as &$code){
      $code['code'] = preg_split($pattern_number, $code['max_code'],2,PREG_SPLIT_NO_EMPTY);
      $code['next_code'] = preg_split($pattern, $code['max_code'],2,PREG_SPLIT_NO_EMPTY);
      
      $manual_codes = Yii::app()->db->createCommand()
              ->select('sct.id, UPPER(prj.real_code) AS real_code, prj.code, prj.is_manual, IF(prj.type_id="3",1,0) AS is_bonus')
              ->from('spi_project prj')
              ->join('spi_school_type sct', 'prj.school_type_id=sct.id')
              ->where('prj.is_manual = 1')
              ->andWhere('prj.real_code IS NOT NULL')
              ->andWhere('prj.code NOT LIKE "%\\\\\\\\%"')
              ->andWhere('prj.code > :max_code',array(':max_code'=>$code['max_code']))
              ->andWhere('prj.real_code = :real_code',array(':real_code'=>$code['real_code']))
              ->order('prj.code')          
              ->queryAll();   
      
      if($manual_codes){    
        ++$code['next_code'][0];
        foreach($manual_codes as $value){
          $value['next_code'] = preg_split($pattern, $value['code'],2,PREG_SPLIT_NO_EMPTY);          
          if($code['next_code'][0] == $value['next_code'][0]){            
            ++$code['next_code'][0]; 
          }else{
            break;
          };
        }            
      }else{
        ++$code['next_code'][0];
      }
     
      if(strlen($code['next_code'][0]) == 1){
          $code['next_code'][0] = "00".$code['next_code'][0];
        }elseif(strlen($code['next_code'][0]) == 2){
          $code['next_code'][0] = "0".$code['next_code'][0];
      };
    }
    
    $result = array (
        'system_code' => 'SUCCESSFUL',
        'code' => '200',
        'next_id' => $custom_codes
    );
    return $result;
  }
  protected function calcResults($result) {
    return $result;
  }

  protected function removeFile($href) {
    unlink($href);
  }

  protected function checkPermission($user, $action, $data) {
    switch ($action) {
      case ACTION_SELECT:
        return $this->isFinance && $user['type'] == TA ? $user['can_view'] && $user['is_finansist'] : $user['can_view'];
      case ACTION_UPDATE:
      case ACTION_INSERT:
      case ACTION_DELETE:
        return $this->isFinance && $user['type'] == TA ? $user['can_edit'] && $user['is_finansist'] : $user['can_edit'];
    }
    return false;
  }

}