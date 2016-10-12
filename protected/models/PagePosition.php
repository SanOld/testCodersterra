<?php
require_once ('utils/utils.php');

class PagePosition extends BaseModel {
  public $table = 'spi_page_position';
  public $post = array();
  public $select_all = ' * ';
  protected function getCommand() {
    $command = Yii::app() -> db -> createCommand() -> select($this->select_all) -> from($this -> table . ' tbl');
    return $command;
  }

  protected function getCommandFilter() {
    $command = Yii::app()->db->createCommand()->select ('tbl.id, tbl.name, tbl.code')
      ->from($this->table  . ' tbl');
    $command = $this->setWhereByRole($command);
    $command->order('name');
    return $command;
  }

  protected function getParamCommand($command, array $params, array $logic = array()) {
    parent::getParamCommand($command, $params);
    $params = array_change_key_case($params, CASE_UPPER);
    if (safe($params, 'PAGE_ID')) {
      $command->andWhere("tbl.page_id = :page_id", array(':page_id' => $params['PAGE_ID']));
    }
    if (safe($params, 'EXCEPT') && $params['EXCEPT'] == 'hint') {
      $sub = Yii::app()->db->createCommand()->select('position_id')->from('spi_hint');
      if (safe($params, 'PAGE_ID')) {
        $sub->where("page_id = :page_id", array(':page_id' => $params['PAGE_ID']));
      }
      $exceptIds = $sub->queryColumn();
      $command->andWhere(array('not in', 'id', $exceptIds));
    }
    return $command;
  }

}
