<?php echo "<?php\n"; ?>
class <?php echo $className; ?> extends UActiveRecord
{
	public static function module()
	{
		return 'base';
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '<?php echo $tableName; ?>';
	}

	public function rules()
	{
		return array(
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('<?php echo implode(', ', array_keys($columns)); ?>', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
<?php foreach($relations as $name=>$relation): ?>
			<?php echo "'$name' => $relation,\n"; ?>
<?php endforeach; ?>
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
<?php foreach($labels as $column=>$label): ?>
			<?php echo "'$column' => \$this->t('$label'),\n"; ?>
<?php endforeach; ?>
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

<?php
foreach($columns as $name=>$column)
{
	if($column->type==='string')
	{
		echo "\t\t\$criteria->compare('$name',\$this->$name,true);\n\n";
	}
	else
	{
		echo "\t\t\$criteria->compare('$name',\$this->$name);\n\n";
	}
}
?>
		return new CActiveDataProvider(__CLASS__, array(
			'criteria'=>$criteria,
		));
	}
}