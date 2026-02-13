<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPriceModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'prices';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'fleet_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'start', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'end', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'start_fee_r', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'time_rate_per_minute_r', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'in_range', 'type' => 'enum', 'default' => 'T')
	);
	
	public static function factory($attr=array())
	{
		return new pjPriceModel($attr);
	}
}
?>