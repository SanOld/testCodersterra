<?php

/**
 * This is the model class for table "spi_user".
 *
 * The followings are the available columns in table 'spi_user':
 * @property integer $id
 * @property string $type
 * @property integer $type_id
 * @property integer $relation_id
 * @property string $login
 * @property string $password
 * @property integer $gender
 * @property string $title
 * @property string $function
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property integer $is_active
 * @property string $auth_token
 * @property string $auth_token_created_at
 * @property string $recovery_token
 */
class NewModel extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'spi_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type_id, login, password, gender, first_name, last_name, email', 'required'),
			array('type_id, relation_id, gender, is_active', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>1),
			array('login, password, title, function, first_name, last_name, email, phone', 'length', 'max'=>45),
			array('auth_token, recovery_token', 'length', 'max'=>32),
			array('auth_token_created_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, type_id, relation_id, login, password, gender, title, function, first_name, last_name, email, phone, is_active, auth_token, auth_token_created_at, recovery_token', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'type_id' => 'Type',
			'relation_id' => 'Relation',
			'login' => 'Login',
			'password' => 'Password',
			'gender' => 'Gender',
			'title' => 'Title',
			'function' => 'Function',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'email' => 'Email',
			'phone' => 'Phone',
			'is_active' => 'Is Active',
			'auth_token' => 'Auth Token',
			'auth_token_created_at' => 'Auth Token Created At',
			'recovery_token' => 'Recovery Token',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('type_id',$this->type_id);
		$criteria->compare('relation_id',$this->relation_id);
		$criteria->compare('login',$this->login,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('gender',$this->gender);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('function',$this->function,true);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('auth_token',$this->auth_token,true);
		$criteria->compare('auth_token_created_at',$this->auth_token_created_at,true);
		$criteria->compare('recovery_token',$this->recovery_token,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NewModel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
