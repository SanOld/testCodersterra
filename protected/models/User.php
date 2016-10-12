<?php
require_once ('utils/utils.php');
require_once ('utils/email.php');


class User extends BaseModel {
  public $table = 'spi_user';
  public $post = array();
  public $select_all = "CONCAT(tbl.last_name, ', ', tbl.first_name) name, IF(tbl.is_active = 1, 'Aktiv', 'Nicht aktiv') status_name, IF(tbl.type = 't' AND tbl.is_finansist, CONCAT(ust.name, ' (F)'), ust.name) type_name, tbl.* ";
  protected function getCommand() {
    $command = Yii::app() -> db -> createCommand() -> select($this->select_all) -> from($this -> table . ' tbl');
    $command -> join('spi_user_type ust', 'tbl.type_id = ust.id');
    $command -> where(' 1=1 ', array());
    return $command;
  }

  protected function getCommandFilter() {
    return Yii::app ()->db->createCommand ()->select ("id, CONCAT(last_name, ' ', first_name) name, IF(tbl.sex = 1, 'Herr', 'Frau') sex, function, phone, title, email, is_finansist")->from ( $this->table  . ' tbl') -> order('name');
  }

  protected function getParamCommand($command, array $params, array $logic = array()) {
    parent::getParamCommand($command, $params);
    $params = array_change_key_case($params, CASE_UPPER);
    $command = $this->setLikeWhere($command,
          array('tbl.first_name', 'tbl.last_name', 'tbl.login', 'tbl.email'),
          safe($params, 'KEYWORD'));
    if (safe($params, 'RELATION_NAME')) {
      $value = $params['RELATION_NAME'];
      $where = array();
      $search_param = array();
      foreach(explode(',', USER_TYPES) as $type) {
        $relation = $this->getRelationByType($type);
        if($relation && safe($relation, 'code')) {
          $command->leftJoin($relation['table'].' '.$relation['prefix'], $relation['prefix'].'.id=tbl.relation_id');
          $where[] = "({$relation['prefix']}.name like :value AND ust.type = '".$type."')";
          $search_param[":value"] = '%' . $value . '%';
        }
      }
      if($where && $search_param) {
        $where = implode(' OR ', $where);
        $command -> andWhere($where, $search_param);
      }
    }
    if (safe($params, 'TYPE_ID')) {
      $command->andWhere("tbl.type_id = :type_id", array(':type_id' => $params['TYPE_ID']));
    }
    if (isset($params['IS_FINANSIST'])) {
      $command -> andWhere("tbl.is_finansist = :is_finansist", array(':is_finansist' => $params['IS_FINANSIST']));
    }
    if (isset($params['IS_ACTIVE']) && in_array($this->user['type'], array('a','p','t'))) {
      $command -> andWhere("tbl.is_active = :is_active", array(':is_active' => $params['IS_ACTIVE']));
    }
    if(!in_array($this->user['type'], array('a','p','t'))) {
      $command -> andWhere("tbl.is_active = 1");
    }
    if(safe($params, 'ORDER') == 'relation_name') {
      $fields = array();
      foreach(explode(',', USER_TYPES) as $type) {
        $relation = $this->getRelationByType($type);
        if($relation && safe($relation, 'code')) {
          $command->leftJoin($relation['table'].' '.$relation['prefix'], $relation['prefix'].'.id=tbl.relation_id AND tbl.type = "'.$type.'"');
          $fields[] = "IFNULL(".$relation['prefix'].".name, '')";
        }
      }
      if($fields) {
        $command->select($this->select_all.", CONCAT(".implode(',', $fields).") relation_name");
      }

    }
    if (safe($params, 'RELATION_ID') && safe($params, 'TYPE')) {
      $command->andWhere("tbl.relation_id = :relation_id AND tbl.type = :type", array(
        ':relation_id' => $params['RELATION_ID'],
        ':type'        => $params['TYPE']
      ));
    } elseif($this->user['relation_id']) {
      $command = $this->setWhereByRole($command);
    }
    if (safe($params, 'AUTH_TOKEN')) {
      $command->andWhere("tbl.auth_token = :auth_token", array(':auth_token' => $params['AUTH_TOKEN']));
    }
    return $command;
  }

  protected function setWhereByRole($command) {
    switch($this->user['type']) {
      case SCHOOL:
        $command->andWhere('(tbl.relation_id = :relation_id AND tbl.type = :type) OR (tbl.type_id IN(1,2)) '.
          'OR (tbl.relation_id IN (SELECT performer_id FROM spi_project WHERE id IN('.
          'SELECT project_id FROM spi_project_school WHERE school_id = :relation_id)) AND tbl.type = "t") '.
          'OR (tbl.relation_id IN(SELECT district_id FROM spi_school WHERE id = :relation_id) AND tbl.type = "d") '.
          'OR (tbl.relation_id IN (SELECT district_id FROM spi_project WHERE id IN(SELECT project_id FROM spi_project_school WHERE school_id = :relation_id)) AND tbl.type = "d")',
          array(':relation_id' => $this->user['relation_id'], ':type' => $this->user['type']));
        break;
      case DISTRICT:
        $command->andWhere('(tbl.relation_id = :relation_id AND tbl.type = :type) '.
          'OR tbl.type_id IN(1,2) '.
          'OR (tbl.relation_id IN(SELECT id FROM spi_school WHERE district_id = :relation_id) AND tbl.type = "s")'.
          'OR (tbl.relation_id IN(SELECT performer_id FROM spi_project WHERE district_id = :relation_id ) AND tbl.type = "t")',
          array(':relation_id' => $this->user['relation_id'], ':type' => $this->user['type']));
        break;
      case TA:
        $command->andWhere('(tbl.relation_id = :relation_id AND tbl.type = :type) OR tbl.type_id IN(1,2)'.
          'OR (tbl.relation_id IN(SELECT district_id FROM spi_project WHERE performer_id = :relation_id ) AND tbl.type = "d")'.
          'OR (tbl.relation_id IN(SELECT school_id FROM spi_project_school WHERE project_id IN(SELECT id FROM spi_project WHERE performer_id = :relation_id)) AND tbl.type = "s")',
          array(':relation_id' => $this->user['relation_id'], ':type' => $this->user['type']));
        break;
    }
    return $command;
  }

  protected function calcResults($result) {
    if(!$this->isFilter) {
      foreach ($result['result'] as &$row) {
        $relation = $this->getRelationByType($row['type']);
        if ($relation && safe($relation, 'table')) {
          $row['relation_name'] = Yii::app()->db->createCommand()->select('name')->from($relation['table'])->where('id=:id', array(':id' => $row['relation_id']))->queryScalar();
        }
        else{
          switch($row['type']) {
            case SCHOOL:
              $row['relation_name'] = Yii::app()->db->createCommand()->select('name')->from('spi_school')->where('id=:id', array(':id' => $this->user['relation_id']))->queryScalar();
              break;
            case DISTRICT:
              $row['relation_name'] = Yii::app()->db->createCommand()->select('name')->from('spi_district')->where('id=:id', array(':id' => $this->user['relation_id']))->queryScalar();
              break;
            case TA:
              $row['relation_name'] = Yii::app()->db->createCommand()->select('short_name')->from('spi_performer')->where('id=:id', array(':id' => $this->user['relation_id']))->queryScalar();
              break;
            case SENAT:
              $row['relation_name'] = "Senat";
              break;
            case ADMIN:
              $row['relation_name'] = "Administrator";
              break;
            case PA:
              $row['relation_name'] = "Stiftung SPI";
              break;
          }
        }
      }
    }
    return $result;
  }

  protected function doAfterSelect($results) {
    foreach($results['result'] as &$row) {
      unset($row['password']);
    }
    return $results;
  }

  protected function doBeforeInsert($post) {
    $this->post = $post;
    $login = safe($post,'login');
    $email = safe($post,'email');

    if(safe($post, 'type_id')) {
      $post['type'] = Yii::app() -> db -> createCommand() -> select('type') -> from('spi_user_type')
        -> where('id=:id ', array(
          ':id' => $post['type_id']))
        -> queryScalar();
      $relation = $this->getRelationByType($post['type']);
      if(!safe($post, 'relation_id') && $relation && safe($relation, 'table')) {
        return array(
          'code' => '409',
          'result' => false,
          'system_code' => 'ERR_MISSED_REQUIRED_PARAMETERS',
          'message' => 'Einfügung ist fehlgeschlagen: Feldbeziehung ist für diesen Benutzertyp erforderlich'
        );
      }
    }

    if ($login && Yii::app() -> db -> createCommand() -> select('*') -> from($this -> table) -> where('login=:login', array(
        ':login' => $login
    )) -> queryRow()) {
      return array(
          'code' => '409',
          'result' => false,
          'system_code' => 'ERR_DUPLICATED'
      );
    }

    if ($email && Yii::app() -> db -> createCommand() -> select('*') -> from($this -> table) -> where('email = :email', array(
        ':email' => $email
    )) -> queryRow()) {
      return array(
        'code' => '409',
        'result' => false,
        'silent' => true,
        'system_code' => 'ERR_DUPLICATED_EMAIL'
      );
    }


    return array(
        'result' => true,
        'params' => $post
    );
  }

  protected function doBeforeUpdate($post, $id) {
    $param = array_change_key_case($post,CASE_UPPER);
    $row = Yii::app() -> db -> createCommand() -> select('*') -> from($this -> table) -> where('id=:id ', array(
        ':id' => $id
    )) -> queryRow();

    if($row['is_finansist'] != $post['is_finansist'] && $row['type'] != 't') {
      return array(
        'code' => '409',
        'result' => false,
        'system_code' => 'ERR_UPDATE_FORBIDDEN',
        'message' => 'Update fehlgeschlagen: Finanzielle Rechte können nicht geändert werden'
      );
    }


    if(safe($post, 'type_id') && $row['type_id'] != $post['type_id'] && !(in_array($row['type_id'], array(3,7)) && in_array($post['type_id'], array(3,7)))) {
      return array(
        'code' => '409',
        'result' => false,
        'system_code' => 'ERR_UPDATE_FORBIDDEN',
        'message' => 'Aktualisierung fehlgeschlagen: Der Typ kann nicht geändert werden'
      );
    }

    if(safe($post, 'relation_id') && $row['relation_id'] != $post['relation_id']) {
      return array(
        'code' => '409',
        'result' => false,
        'system_code' => 'ERR_UPDATE_FORBIDDEN',
        'message' => 'Update fehlgeschlagen: Die Beziehung kann nicht geändert werden'
      );
    }

    if($id == $this->user['id'] && !($this->user['can_edit'] && $this->user['type_id'] != 2) && $row['login'] != $post['login']) {
      return array(
        'code' => '409',
        'result' => false,
        'system_code' => 'ERR_UPDATE_FORBIDDEN',
        'message' => 'Update fehlgeschlagen: Der Benutzername kann nicht geändert werden'
      );
    }

    if (Yii::app() -> db -> createCommand()
        -> select('*')
        -> from($this -> table)
        -> where('id != :id AND login=:login',
                  array(':id' => $id, ':login' => $post['login']))
         -> queryRow()) {
      return array(
          'code' => '409',
          'result' => false,
          'silent' => true,
          'system_code' => 'ERR_DUPLICATED'
      );
    }

    if (isset($param['EMAIL']) && $param['EMAIL'] && Yii::app() -> db -> createCommand()
        -> select('*')
        -> from($this -> table)
        -> where('email = :email AND id!=:id',
                  array(
                  ':email' => $param['EMAIL'],
                  ':id' => $id))
         -> queryRow()) {
      return array(
        'code' => '409',
        'result' => false,
        'silent' => true,
        'system_code' => 'ERR_DUPLICATED_EMAIL'
      );
    }

    if(isset($param['PASSWORD']) && !$param['PASSWORD']) {
      unset($param['PASSWORD']);
      unset($post['password']);
    }

    if (isset($param['PASSWORD']) && $this->user['id'] == $row['id'] && md5($param['OLD_PASSWORD']) != $row['password']) {
      return array(
        'code' => '409',
        'result' => false,
        'silent' => true,
        'system_code' => 'ERR_CURRENT_PASSWORD'
      );
    }

    unset($post['old_password']);


    if(isset($post['is_finansist']) && safe($post, 'is_finansist') != safe($row, 'is_finansist')) {
      $post['auth_token'] = '';
    }

    if(safe($post, 'is_virtual')) {
      $post['password'] = '';
    }

    if($row['relation_id'] && safe($post, 'is_active') == 0 && safe($row, 'is_active') == 1) {
      if($this->isRelatedUser($row)) {
        return array(
          'code' => '409',
          'result' => false,
          'system_code' => 'ERR_UPDATE_FORBIDDEN',
          'message' => 'Dieser Benutzer ist verantwortlich'
        );
      }
    }

    return array(
        'result' => true,
        'params' => $post
    );
  }

  protected function doAfterUpdate($result, $params, $post, $id) {
    if(safe($post, 'password')) {
      $user = Yii::app() -> db -> createCommand()
        -> select('*')
        -> from($this->table)
        -> where('id=:id', array(':id' => $id))
        ->queryRow();
      Email::doUpdatePassword($user, $post['password']);
    }
    return $result;
  }

  protected function doAfterInsert($result, $params, $post) {
    if($result['result']) {
      Email::doWelcome($result);
    }
    return $result;
  }

  protected function checkPermission($user, $action, $data) {
    switch ($action) {
      case ACTION_SELECT:
        return $user['can_view'] || $user['id'] == $this->id;
      case ACTION_UPDATE:
        return $user['can_edit'] || $user['id'] == $this->id;
      case ACTION_INSERT:
        return $user['can_edit'] && !(in_array($user['type_id'], array(6,2)) && $data['type_id'] == 1); // except PA & Senat create Admin
      case ACTION_DELETE:
        return $user['can_edit'] && $user['type_id'] != 2; // except PA
    }
    return false;
  }

  protected function doBeforeDelete($id) {
    $row = Yii::app() -> db -> createCommand() -> select('*') -> from($this -> table) -> where('id=:id', array(
      ':id' => $id
    )) -> queryRow();
    if (!$row) {
      return array(
        'code' => '409',
        'result' => false,
        'system_code' => 'ERR_NOT_EXISTS'
      );
    }

    if($this->isRelatedUser($row)) {
      return array(
        'code' => '409',
        'result' => false,
        'system_code' => 'ERR_UPDATE_FORBIDDEN',
        'message' => 'Dieser Benutzer ist verantwortlich'
      );
    }

    return array(
      'result' => true
    );
  }

  private function isRelatedUser($row) {
    $table = $field = '';
    switch ($row['type']) {
      case 's':
        $table = 'spi_school';
        $field = 'contact_id';
        break;
      case 'd':
        $table = 'spi_district';
        $field = 'contact_id';
        break;
      case 't':
        $table = 'spi_performer';
        $field = 'representative_user_id';
        break;
    }
    if($table && $field) {
      if(Yii::app() -> db -> createCommand() -> select('id')
        -> from($table) -> where("$field = :field", array(':field' => $row['id']))
        -> queryScalar()) {
        return true;
      }
    }
    return false;
  }


}
