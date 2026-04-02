<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFleetModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'fleets';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'start_fee', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'time_rate_per_minute', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'overbooking_cost', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'price_hike', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'fee_per_person', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'numberof_booking', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'passengers', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'luggage', 'type' => 'int', 'default' => ':NULL'),		
		array('name' => 'category', 'type' => 'int', 'default' => ':NULL'),		
		array('name' => 'source_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'thumb_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'image_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	public $i18n = array('fleet', 'description');
	
	public static function factory($attr=array())
	{
		return new pjFleetModel($attr);
	}
}
?>