<?php
require_once ('utils/utils.php');
//require_once ('utils/responce.php');

class Page extends BaseModel {
  public $table = 'spi_page';
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


  protected function getParamCommand($command, array $params, array $logic = array()) {
    parent::getParamCommand($command, $params);
    $params = array_change_key_case($params, CASE_UPPER);

    if(safe($params, 'RIGHT') && safe($params, 'TYPE_ID')) {
      $command->select('tbl.*, utr.id right_id, IFNULL(utr.can_view, 0) can_view, IFNULL(utr.can_edit, 0) can_edit, IFNULL(utr.can_show, 0) can_show');
      $command->leftJoin('spi_user_type_right utr', 'tbl.id=utr.page_id AND utr.type_id=:type_id', array(':type_id' => $params['TYPE_ID']));
    }
    if(!safe($params, 'ALL')) {
      $command->andWhere('tbl.is_real_page = 1');
    }
    if(!safe($params, 'SYSTEM')) {
      $command->andWhere('tbl.is_system = 0');
    }
    return $command;
  }

}
