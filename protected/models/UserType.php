<?php
require_once ('utils/utils.php');
//require_once ('utils/responce.php');

class UserType extends BaseModel {
  public $table = 'spi_user_type';
  public $post = array();
  public $select_all = ' tbl.* , (SELECT name FROM spi_user_type typ WHERE typ.type=tbl.type AND typ.default=1 LIMIT 1) AS `relation_name`';
  protected function getCommand() {
    $command = Yii::app() -> db -> createCommand() -> select($this->select_all) -> from($this -> table . ' tbl');

    $where = ' 1=1 ';
    $conditions = array();

    if ($where) {
      $command -> where($where, $conditions);
    }
    
    return $command;
  }

  protected function getCommandFilter() {
    return Yii::app ()->db->createCommand ()->select ("tbl.id, tbl.name, tbl.type")->from ( $this->table  . ' tbl') -> order('name');
  }

  protected function getParamCommand($command, array $params, array $logic = array()) {
    parent::getParamCommand($command, $params);
    $params = array_change_key_case($params, CASE_UPPER);
    if (safe($params, 'TYPE')) {
      $command->andWhere("tbl.type = :type", array(':type' => $params['TYPE']));
    } else if($this->user['relation_id']) {
      $command = $this->setWhereByRole($command);
    }
    if (safe($params, 'DEFAULT')) {
      $command->andWhere("tbl.default = :def", array(':def' => $params['DEFAULT']));
    }
    if(safe($params, 'USER_CREATE')) {
      switch($this->user['type']) {
        case SCHOOL:
        case TA:
        case DISTRICT:
          $command->andWhere("tbl.type = :type", array(':type' => $this->user['type']));
          break;
        case ADMIN:
          if($this->user['type_id'] == 2) {
            $command->andWhere("tbl.id != 1");
          } else if($this->user['type_id'] == 6) {
            $command->andWhere("tbl.id NOT IN(1,2)");
          }
      }
    }
    return $command;
  }

  protected function setWhereByRole($command) {
    switch($this->user['type']) {
      case SCHOOL:
        $command->join('spi_user usr', 'usr.type_id = tbl.id');
        $command->andWhere('(tbl.id IN(1,2)) OR (usr.relation_id = :relation_id AND tbl.type = :type) '.
          'OR (usr.relation_id IN (SELECT performer_id FROM spi_project WHERE id IN('.
          'SELECT project_id FROM spi_project_school WHERE school_id = :relation_id)) AND tbl.type = "t") '.
          'OR (usr.relation_id IN (SELECT school_id FROM spi_project_school WHERE id IN('.
          'SELECT project_id FROM spi_project_school WHERE school_id = :relation_id)) AND tbl.type = "s") '.
          'OR (usr.relation_id IN(SELECT district_id FROM spi_school WHERE id = :relation_id) OR (usr.relation_id IN (SELECT district_id FROM spi_project WHERE id IN('.
          'SELECT project_id FROM spi_project_school WHERE school_id = :relation_id))) AND tbl.type = "d") ',
          array(':relation_id' => $this->user['relation_id'], ':type' => $this->user['type']));
        $command->group('tbl.id');
        break;
      case DISTRICT:
        $command->join('spi_user usr', 'usr.type_id = tbl.id');
        $command->andWhere('tbl.id IN(1,2) OR (usr.relation_id = :relation_id AND tbl.type = :type) '.
          'OR (usr.relation_id IN(SELECT id FROM spi_school WHERE district_id = :relation_id) AND tbl.type = "s")'.
          'OR (usr.relation_id IN(SELECT performer_id FROM spi_project WHERE district_id = :relation_id) AND tbl.type = "t")',
          array(':relation_id' => $this->user['relation_id'], ':type' => $this->user['type']));
        $command->group('tbl.id');
        break;
      case TA:
        $command->join('spi_user usr', 'usr.type_id = tbl.id');
        $command->andWhere('(tbl.id IN(1,2)) OR (usr.relation_id = :relation_id AND tbl.type = :type) '.
          'OR (usr.relation_id IN(SELECT school_id FROM spi_project_school WHERE project_id IN(SELECT id FROM spi_project WHERE performer_id = :relation_id)) AND tbl.type = "s")'.
          'OR (usr.relation_id IN(SELECT district_id FROM spi_project WHERE performer_id = :relation_id) AND tbl.type = "d")',
          array(':relation_id' => $this->user['relation_id'], ':type' => $this->user['type']));
        $command->group('tbl.id');
        break;
    }
    return $command;
  }

//  protected function doAfterSelect($results) {
//    foreach($results['result'] as &$row) {
//      if(safe($row, 'type')) {
//        $relation = $this->getRelationByType($row['type']);
//        $row['relation_name'] = $relation['name'];
//        $row['relation_code'] = safe($relation, 'code', '');
//      }
//    }
//    return $results;
//  }

  protected function doBeforeInsert($post) {
    $this->post = $post;
    $name = safe($post,'name');
    if(safe($post, 'rights')) {
      unset($post['rights']);
    }

    if ($name && Yii::app() -> db -> createCommand() -> select('*') -> from($this -> table) -> where('name=:name', array(
        ':name' => $name
      )) -> queryRow()) {
      return array(
        'code' => '409',
        'result' => false,
        'system_code' => 'ERR_DUPLICATED_NAME',
        'message' => 'Insert failed: This role name already exists.'
      );
    }

    return array(
      'result' => true,
      'params' => $post
    );
  }

  protected function doAfterInsert($result, $params, $post) {
    if(safe($post, 'rights') && $result['code'] == '200' && safe($result, 'id')) {
      foreach($post['rights'] as $right) {
        Yii::app ()->db->createCommand()->insert('spi_user_type_right', array(
          'type_id'  => $result['id'],
          'page_id'  => $right['page_id'],
          'can_view' => $right['can_view'],
          'can_edit' => $right['can_edit'],
          'can_show' => $right['can_show'],
        ));
      }
    }
    return $result;
  }

  protected function doBeforeUpdate($post, $id) {
    $name = safe($post,'name');
    if(safe($post, 'rights')) {
      unset($post['rights']);
    }
    if($this->isDefaultType($id)) {
      unset($post['type']);
    }
    if ($name && Yii::app() -> db -> createCommand() -> select('*') -> from($this -> table) -> where('id!=:id AND name=:name', array(
        ':id' => $id,
        ':name' => $name
      )) -> queryRow()) {
      return array(
        'code' => '409',
        'result' => false,
        'system_code' => 'ERR_DUPLICATED_NAME',
        'message' => 'Update failed: This role name already exists.'
      );
    }

    return array(
      'result' => true,
      'params' => $post
    );
  }
  protected function isDefaultType($id) {
    return Yii::app()->db->createCommand()
      ->select('default')
      ->from($this -> table)
      ->where('id=:id', array(':id'=> $id))
      ->queryScalar();
  }

  protected function doAfterUpdate($result, $params, $post, $id) {
//    print_r($post['rights']);
//    echo(safe($post, 'rights') && $result['code'] == '200' && (!$this->isDefaultType($id) || $this->user['is_super_user']));
    if(safe($post, 'rights') && $result['code'] == '200' && (!$this->isDefaultType($id) || $this->user['is_super_user'])) {
      foreach($post['rights'] as $right) {
        if(!safe($right, 'id')) {
          Yii::app ()->db->createCommand()->insert('spi_user_type_right', array(
            'type_id'  => $id,
            'page_id'  => $right['page_id'],
            'can_view' => $right['can_view'],
            'can_edit' => $right['can_edit'],
            'can_show' => $right['can_show'],
          ));
        } else {
          Yii::app ()->db->createCommand()->update('spi_user_type_right', array(
            'can_view' => $right['can_view'],
            'can_edit' => $right['can_edit'],
            'can_show' => $right['can_show'],
          ), 'id=:id', array (':id' => $right['id']));
        }
      }
    }
//    die;
    return $result;
  }

  protected function doBeforeDelete($id) {
    $user = Yii::app() -> db -> createCommand() -> select('*') -> from($this -> table . ' tbl') -> where('id=:id', array(
        ':id' => $id 
    )) -> queryRow();
    if (!$user) {
      return array(
          'code' => '409',
          'result' => false,
          'system_code' => 'ERR_NOT_EXISTS' 
      );
    } else if($this->isDefaultType($id)) {
      return array(
        'code' => '403',
        'result' => false,
        'system_code' => 'ERR_FORBIDDEN'
      );
    }
    
    return array(
        'result' => true 
    );
  }

  protected function getForeignKeyError($e) {
    return array(
      'code' => '409',
      'result'=> false,
      'system_code'=> 'ERR_DEPENDENT_RECORD',
      'message' => 'Diese Rolle zu löschen ist nicht möglich. Sie müssen zuerst Benutzer mit dieser Rolle löschen'
    );
  }


}
