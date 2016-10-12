<?php
require_once ('utils/utils.php');


class UserTypeRight extends BaseModel {
  public $table = 'spi_user_type_right';
  public $post = array();
  public $select_all = ' * ';
  protected function getCommand() {
    $command = Yii::app() -> db -> createCommand() -> select($this->select_all) -> from($this -> table . ' tbl');
    $where = ' 1=1 ';
    $conditions = array();
    if ($where) {
      $command -> where($where, $conditions);
    }
    return $command;
  }

  protected function doAfterSelect($results) {
    $pages = Yii::app() -> db -> createCommand() -> select('*') -> from('spi_page tbl')->queryAll ();
    $pages_dict = array();
    foreach($pages as $page) {
      $pages_dict[$page['id']] = $page;
    }
    foreach($results['result'] as &$row) {
      $row['page_name'] = $pages_dict[$row['page_id']]['name'];
    }
    return $results;
  }

  protected function getParamCommand($command, array $params, array $logic = array()) {
    parent::getParamCommand($command, $params);
    $params = array_change_key_case($params, CASE_UPPER);
    if(safe($params, 'TYPE_ID') === NULL) {
      return NULL;
    }
    $command->andWhere(' tbl.type_id=:type_id ',array(':type_id' => $params['TYPE_ID']));
    return $command;
  }


}
