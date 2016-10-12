<?php
require_once ('utils/utils.php');


class Request extends BaseModel {
  public $table = 'spi_request';
  public $post = array();
  public $school_concepts = array();
  public $finance_plan = array();
  public $school_goals = array();
  public $select_all = "tbl.*
                      , prf.short_name performer_name
                      , prf.is_checked performer_is_checked
                      , rqs.name status_name
                      , rqs.code status_code
                      , prj.code code
                      , fns.programm";

  public $paPriority = array('in_progress' => 1, 'rejected' => 2, 'unfinished' => 3, 'accepted' => 4 );
  public $taPriority = array('rejected' => 1, 'unfinished' => 2, 'in_progress' => 3, 'accepted' => 4 );

  protected function getCommand() {
    if(safe($_GET, 'list') == 'year') {
      $command = Yii::app() -> db -> createCommand()->select('year')->from($this -> table . ' tbl')->group('year');
      $command -> join('spi_project prj','tbl.project_id = prj.id' );
    } elseif (isset($_GET['id'])){
      $this -> select_all = "tbl.*
                            , prj.id project_id
                            , prj.code code
                            , IF(prj.type_id = 3, 1, 0) is_bonus_project

                            , rqs.code status_code

                            , prf.id performer_id
                            , prf.is_checked performer_is_checked
                            , CONCAT( 'Überpüft von ',
                                (SELECT CONCAT (u.first_name, ' ', u.last_name) name
                                   FROM spi_user u
                                  WHERE u.id = prf.checked_by), ' ',
                                  DATE_FORMAT(prf.checked_date,'%d.%m.%Y')

                              ) performer_checked_by
                            , prf.short_name performer_name
                            , prf.name performer_long_name
                            , prf.address performer_address
                            , prf.plz performer_plz
                            , prf.city performer_city
                            , prf.homepage performer_homepage
                            , prf.phone performer_phone
                            , prf.fax performer_fax
                            , prf.email performer_email
                            , prf_user.function performer_contact_function
                            , CONCAT(IF(prf_user.sex = 1, 'Herr', 'Frau' ), ' ' , prf_user.first_name, ' ', prf_user.last_name) performer_contact

                            , dst.id district_id
                            , dst.name district_name
                            , dst.address district_address
                            , dst.plz district_plz
                            , dst.city district_city
                            , dst.phone district_phone
                            , dst.fax district_fax
                            , dst.email district_email
                            , dst.homepage district_homepage
                            , CONCAT(IF(user.sex = 1, 'Herr', 'Frau' ), ' ' , user.first_name, ' ', user.last_name) district_contact

                            ";
      $command = Yii::app() -> db -> createCommand() -> select($this->select_all) -> from($this -> table . ' tbl');
      $command -> join(     'spi_request_status rqs',     'tbl.status_id           = rqs.id' );
      $command -> join(     'spi_performer prf',          'tbl.performer_id        = prf.id' );
      $command -> leftJoin( 'spi_user prf_user',          'prf_user.id             = prf.representative_user_id' );
      $command -> join(     'spi_project prj',            'tbl.project_id          = prj.id' );
      $command -> leftJoin( 'spi_district dst',           'dst.id                  = prj.district_id' );
      $command -> leftJoin( 'spi_user user',              'user.id                 = dst.contact_id' );
      $command -> where(' 1=1 ', array());

    } else {
      $command = Yii::app() -> db -> createCommand() -> select($this->select_all) -> from($this -> table . ' tbl');
      $command -> join( 'spi_request_status rqs', 'tbl.status_id           = rqs.id' );
      $command -> leftJoin( 'spi_performer prf',      'tbl.performer_id        = prf.id' );
      $command -> join( 'spi_project prj',        'tbl.project_id          = prj.id' );
      $command -> join( 'spi_finance_source fns', 'prj.type_id = fns.project_type_id' );
      $command -> where(' 1=1 ', array());

    }
    return $command;
  }

  protected function getParamCommand($command, array $params, array $logic = array()) {
    parent::getParamCommand($command, $params);
    $params = array_change_key_case($params, CASE_UPPER);
    if(safe($params, 'PROJECT_CODE')) {
      $command = $this->setLikeWhere($command, array('prj.code'), safe($params, 'PROJECT_CODE'));
    }
    if(safe($params, 'YEAR')) {
      $command -> andWhere('tbl.year = :year', array(':year' => $params['YEAR']));
    }
    if(safe($params, 'PERFORMER_ID')) {
      $command -> andWhere('prf.id = :performer_id', array(':performer_id' => $params['PERFORMER_ID']));
    }
    if(safe($params, 'PROJECT_TYPE_ID')) {
      $command -> andWhere('prj.type_id = :type_id', array(':type_id' => $params['PROJECT_TYPE_ID']));
    }
    if(safe($params, 'SCHOOL_TYPE_ID')) {
      $command -> andWhere('prj.school_type_id = :type_id', array(':type_id' => $params['SCHOOL_TYPE_ID']));
    }
    if(safe($params, 'STATUS_ID')) {
      if(!is_int($params['STATUS_ID'])) {
        $values = explode(',', $params['STATUS_ID']);
      } else {
        $values = array($params['STATUS_ID']);
      }
      $command -> andWhere(array('in', 'rqs.id', $values));
    }
    $command = $this->setWhereByRole($command);
    return $command;
  }

  protected function setWhereByRole($command) {
    switch($this->user['type']) {
      case SCHOOL:

        $command->join('spi_project_school sps', 'sps.project_id=tbl.project_id');
        $command->andWhere("sps.school_id = :school_id", array(':school_id' => $this->user['relation_id']));
        break;
      case DISTRICT:
        $command->andWhere("prj.district_id = :district_id", array(':district_id' => $this->user['relation_id']));
        break;
      case TA:
        $command->andWhere("prj.performer_id = :performer_id", array(':performer_id' => $this->user['relation_id']));
        break;
    }
    return $command;
  }

  protected function calcResults($result) {
    if(safe($_GET, 'list') == 'year') {
      foreach($result['result'] as &$row) {
        $row = (int)$row['year'];
      }
      if(!in_array(date("Y"), $result['result'])) {
        array_push($result['result'], (int)date("Y"));
      }
    } else {
      foreach($result['result'] as &$row) {

        if($row['start_date']   == '0000-00-00'){ $row['start_date']  = ''; }
        if($row['due_date']     == '0000-00-00'){ $row['due_date']    = ''; }
        if($row['last_change']  == '0000-00-00'){ $row['last_change'] = ''; }
        if($row['end_fill']     == '0000-00-00'){ $row['end_fill']    = ''; }

        $row['status_goal'] = $this->calcGoalsStatus($row['id']);
        $row['status_concept'] = $this->calcConceptStatus($row['id']);
        $row['is_action_required'] = $this->isActionRequired(array(
                                                                  $row['status_goal']
                                                                  ,$row['status_concept']
                                                                  ,$row['status_finance']
        ));

      }
    }
    return $result;
  }


  protected function calcConceptStatus($ID) {
    $statusPriority = in_array($this->user['type'], array('a', 'p')) ? $this->paPriority : $this->taPriority;
    $RequestSchoolConcept = CActiveRecord::model('RequestSchoolConcept');
    $RequestSchoolConcept->user = $this->user;
    return $RequestSchoolConcept->getCommonStatus($ID, $statusPriority);
  }

  protected function calcGoalsStatus($ID) {
    $resultStatus = 'unfinished';
     if($this->user['type'] == 'a' || $this->user['type'] == 'p'){
       $priority = $this->paPriority;
     } else {
       $priority = $this->taPriority;
     }
    $RequestSchoolGoal = CActiveRecord::model('RequestSchoolGoal');
    $RequestSchoolGoal ->user = $this->user;
    $resultStatus = $RequestSchoolGoal->calcStatus($ID, $priority );

    return $resultStatus;
  }

  protected function isActionRequired($statuses) {
    foreach($statuses as $status) {
      if($this->user['type'] == 'a' || $this->user['type'] == 'p'){
        if($status == 'in_progress' ){
          return true;
        }
      } else {
        if($status == 'rejected' ){
          return true;
        }
      }
    }
    return false;
  }


  protected function doBeforeInsert($post) {
    if($this->user['type'] == ADMIN || ($this->user['type'] == PA)) {

      if(Yii::app() -> db -> createCommand() -> select('*') -> from($this -> table) -> where('project_id=:project_id AND year=:year', array(
          ':project_id' => safe($post,'project_id'),
          ':year' => safe($post,'year')
      )) -> queryRow()) {
        return array(
            'code' => '409',
            'result' => false,
            'system_code' => 'ERR_DUPLICATED',
            'message' => 'This project already exists'
        );
      }

      $post['performer_id'] = Yii::app() -> db -> createCommand()
                              -> select('performer_id') -> from('spi_project')
                              -> where('id=:id ', array(':id' => safe($post,'project_id')))
                              ->queryScalar();
      return array(
          'result' => true,
          'params' => $post
      );
    } else {
      return array(
          'code' => '403',
          'result' => false,
          'system_code' => 'ERR_PERMISSION',
          'message' => 'Only Admin can create the projects'
      );

    }
  }
  protected function doAfterInsert($result, $params, $post) {
    if($result['code'] == '200' && safe($result, 'id')) {
      $RequestSchoolConcept = CActiveRecord::model('RequestSchoolConcept');
      $RequestSchoolConcept ->user = $this->user;
      $RequestSchoolFinance = CActiveRecord::model('RequestSchoolFinance');
      $RequestSchoolFinance ->user = $this->user;
      $RequestSchoolGoal = CActiveRecord::model('RequestSchoolGoal');
      $RequestSchoolGoal ->user = $this->user;

      $school_ids = Yii::app() -> db -> createCommand()
        -> select('prs.school_id')
        -> from('spi_project_school prs')
        -> join('spi_request req', 'req.project_id = prs.project_id')
        -> where('req.id=:id', array(':id' => $result['id']))
        -> queryColumn();

      $rate = Yii::app() -> db -> createCommand()
        -> select('rate')
        -> from('spi_project')
        -> where('id=:id', array(':id' => $params['project_id']))
        -> queryScalar();

      foreach($school_ids as $school_id) {
        $data = array(
          'request_id' => $result['id'],
          'school_id'  => $school_id,
        );
        $RequestSchoolConcept->insert($data, true);

        $data['rate'] = $rate;
        $data['overhead_cost'] = 1800;
        $RequestSchoolFinance->insert($data, true);
        for ($i=1; $i<=5; $i++){
          $opt = 0;
          if ($i > 3){$opt = 1;}
          $goalData = array(
            'request_id' => $result['id'],
            'school_id'  => $school_id,
            'goal_id'  => $i,
            'option'  => $opt,
            'name' => 'Entwicklungsziel ' . $i
          );
          $RequestSchoolGoal->insert($goalData, true);
        }
      }

    }

    return $result;
  }

  protected function doAfterUpdate($result, $params, $post, $request_id) {
    Yii::app()->db->createCommand()->update($this->table, array('last_change' => date("Y-m-d", time())), 'id=:id', array(':id' => $request_id ));
    if($this->school_concepts) {
      $RequestSchoolConcept = CActiveRecord::model('RequestSchoolConcept');
      $RequestSchoolConcept->user = $this->user;
      foreach ($this->school_concepts as $id=>$data) {
        $RequestSchoolConcept->update($id, $data, true);
      }
    }

    if($this->school_goals) {
      $RequestSchoolGoal = CActiveRecord::model('RequestSchoolGoal');
      $RequestSchoolGoal->user = $this->user;
      foreach ($this->school_goals as $id=>$data) {
        $RequestSchoolGoal->update($id, $data, true);
      }
    }

    if($this->finance_plan) {
      $RequestSchoolFinance = CActiveRecord::model('RequestSchoolFinance');
      $RequestSchoolFinance ->user = $this->user;

      if(in_array($this->user['type'], array('a', 'p'))) {
        foreach (safe($this->finance_plan, 'schools', array()) as $data) {
          $id = $data['id'];
          unset($data['id']);
          if($data['rate']){
            $data['rate'] = (float)str_replace(",", ".", $data['rate']);
          }
          $res = $RequestSchoolFinance->update($id, $data, true);
        }
      }

      if(safe($this->finance_plan, 'prof_associations', array())) {
        $RequestProfAssociation = CActiveRecord::model('RequestProfAssociation');
        $RequestProfAssociation ->user = $this->user;

        foreach ($this->finance_plan['prof_associations'] as $data) {
          if($id = safe($data,'id')) {
            unset($data['id']);
            if(safe($data,'is_deleted')) {
              $RequestProfAssociation->delete($id, true);
            } else {
              $RequestProfAssociation->update($id, $data, true);
            }
          } elseif(!safe($data,'is_deleted')) {
            $data['request_id'] = $request_id;
            $res = $RequestProfAssociation->insert($data, true);
          }
        }
      }

      if(safe($this->finance_plan,'users', array())) {
        $RequestUser = CActiveRecord::model('RequestUser');
        $RequestUser ->user = $this->user;
        foreach ($this->finance_plan['users'] as $data) {
          if($id = safe($data,'id')) {
            unset($data['id']);
            if(safe($data,'is_deleted')) {
              $RequestUser->delete($id, true);
            } else {
              $RequestUser->update($id, $data, true);
            }
          } else {
            $data['request_id'] = $request_id;
            $RequestUser->insert($data, true);
          }
        }
      }
    }
    return $result;
  }

  protected function doAfterSelect($result) {

    if (isset($_GET['id'])){
      $row = $result['result'][0];

      $row['schools'] = Yii::app() -> db -> createCommand()
                                      -> select("sch.*
                                                ,CONCAT(IF(user.sex = 1, 'Herr', 'Frau' ), ' ' , user.first_name, ' ', user.last_name)  user_name
                                                , user.function user_function")
                                      -> from('spi_project_school prj_sch')
                                      -> join( 'spi_school sch', 'prj_sch.school_id = sch.id' )
                                      -> leftJoin( 'spi_user user', 'user.id = sch.contact_id' )
                                      -> where('prj_sch.project_id=:id', array(':id' => $row['project_id']))
                                      -> queryAll();
      $result['result'] =  $row;
    }

    return $result;
  }

  protected function doBeforeUpdate($post, $id) {

    unset($post['status_code']);

    if(isset($post['doc_target_agreement_id']) && !$post['doc_target_agreement_id']) {
      $post['doc_target_agreement_id'] = null;
    }
    if(isset($post['doc_financing_agreement_id']) && !$post['doc_financing_agreement_id']) {
      $post['doc_financing_agreement_id'] = null;
    }
    if(isset($post['doc_request_id']) && !$post['doc_request_id']) {
      $post['doc_request_id'] = null;
    }

    if(isset($post['school_concepts'])) {
      $this->school_concepts = $post['school_concepts'];
      unset($post['school_concepts']);
    }

    if(isset($post['school_goals'])) {
      $this->school_goals = $post['school_goals'];
      unset($post['school_goals']);
    }

    if(isset($post['finance_plan'])) {
      $this->finance_plan = $post['finance_plan'];
      unset($post['finance_plan']);
    }

    return array (
      'result' => true,
      'params' => $post,
      'post' => $post
    );

  }

  protected function doBeforeDelete($id) {
    $exists = Yii::app() -> db -> createCommand() -> select('tbl.id') -> from($this -> table . ' tbl') -> where('id=:id', array(
      ':id' => $id
    )) -> queryScalar();
    if (!$exists) {
      return array(
        'code' => '409',
        'result' => false,
        'system_code' => 'ERR_NOT_EXISTS'
      );
    }

    $RequestSchoolConcept = CActiveRecord::model('RequestSchoolConcept');
    $RequestSchoolConcept->user = $this->user;
    if($concepts = Yii::app() -> db -> createCommand() -> select('tbl.id') -> from('spi_request_school_concept tbl') -> where('request_id=:id', array(
      ':id' => $id
    )) -> queryAll()) {
      foreach($concepts as $concept) {
        $RequestSchoolConcept->delete($concept['id'], true);
      }
    }

    $RequestSchoolGoal = CActiveRecord::model('RequestSchoolGoal');
    $RequestSchoolGoal->user = $this->user;
    if($goals = Yii::app() -> db -> createCommand() -> select('tbl.id') -> from('spi_request_school_goal tbl') -> where('request_id=:id', array(
      ':id' => $id
    )) -> queryAll()) {
      foreach($goals as $goal) {
        $RequestSchoolGoal->delete($goal['id'], true);
      }
    }

    return array(
      'result' => true
    );
  }
}
