<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjDateRangeModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'fleets_date_range_prices';
	
	protected $schema = array(
			array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
			array('name' => 'fleet_id', 'type' => 'int', 'default' => 0),
			array('name' => 'from_date', 'type' => 'datetime', 'default' => ':NULL'),
			array('name' => 'to_date', 'type' => 'datetime', 'default' => ':NULL'),
			array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
			array('name' => 'status', 'type' => 'enum', 'default' => 'T')
		);

	public $i18n = array('name');

	public static function factory($attr=array())
	{
		return new pjDateRangeModel($attr);
	}
}
?>