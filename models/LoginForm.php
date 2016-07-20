<?php

/**
 * 验证登录
 * @author 80071519
 *
 */
class LoginForm extends CFormModel {
	public $userId;
	public $userName;
	public $password;
	public $tarGet;
	public $isKey;
	public $application;
	public static function model($className = __CLASS__) {
		return parent::model ( $className );
	}
 
	/**
	 *
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
				array ('userName, password','required' ),
				array ('userName,password','length','max' => 50),
				array ('userName','length','min' => 2),
				array ('password','length','min' => 6),
				array ('userName, password,tarGet,application','safe') 
		);
	}
	/**
	 *
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
				'userId' => '用户Id',
				'userName' => '用户名',
				'password' => '密码',
				'tarGet' => '上一级链接', 
				'application' => '应用', 
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
	 *         based on the search/filter conditions.
	 */
	public function search() {
		// @todo Please modify the following code to remove attributes that should not be searched.
		$criteria = new CDbCriteria ();
		
		$criteria->compare ( 'userId', $this->userId, true );
		$criteria->compare ( 'userName', $this->userName );
		$criteria->compare ( 'password', $this->password );
		$criteria->compare ( 'tarGet', $this->tarGet );
		$criteria->compare ( 'application', $this->application );
		
		return new CActiveDataProvider ( $this, array (
				'criteria' => $criteria 
		) );
	}
	
}
