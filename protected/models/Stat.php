<?php
require_once ('utils/utils.php');
//require_once ('utils/responce.php');

class Stat extends BaseModel {
  public $table = 'visit';
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

    return $command;
  }
  
   protected function doBeforeInsert($post) {
//     $command = Yii::app() -> db -> createCommand() -> select($this->select_all) -> from($this -> table . ' tbl');
//     if(isset($post['date'])){
//       
//     }
     
    return array (
        'result' => true,
        'params' => $post,
        'post' => $post
    );
  }

}
