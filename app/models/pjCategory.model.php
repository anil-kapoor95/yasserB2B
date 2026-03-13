<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}

class pjCategoryModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'categories';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'category', 'type' => 'varchar', 'default' => ':NULL')
	);
	
	// i18n field
	public $i18n = array('category');

	public static function factory($attr = array())
	{
		return new pjCategoryModel($attr);
	}
}
?>