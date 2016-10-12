<?php
require_once ('utils/utils.php');


class Visit extends BaseModel {
  public $table = 'spi_visit';
  public $post = array();
  public $select_all = ' tbl.* ';


  protected function getCommand() {
    $command = Yii::app() -> db -> createCommand() -> select($this->select_all)
      -> from($this -> table . ' tbl')
      -> leftJoin('browser', 'tbl.browser_id = browser.id')
      -> leftJoin('city', 'tbl.city_id = city.id')
      -> leftJoin('cookie', 'tbl.cookie_id = cookie.id')
      -> leftJoin('hint', 'tbl.hint_id = hint.id')
      -> leftJoin('ip', 'tbl.ip_id = ip.id')
      -> leftJoin('os', 'tbl.os_id = os.id')
      -> leftJoin('ref', 'tbl.ref_id = ref.id');


    $where = ' 1=1 ';
    $conditions = array();

    if ($where) {
      $command -> where($where, $conditions);
    }

    print_r($command->text);
    return $command;
  }

  protected function getParamCommand($command, array $params, array $logic = array()) {
    parent::getParamCommand($command, $params);

    return $command;
  }






}
