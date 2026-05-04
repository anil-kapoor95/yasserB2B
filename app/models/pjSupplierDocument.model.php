<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjSupplierDocumentModel extends pjAppModel
{
	protected $primaryKey = 'id';
    protected $table = 'supplier_documents';

    protected $schema = array(
        array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'supplier_id', 'type' => 'int', 'default' => ':NULL'),

        array('name' => 'file_name', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'original_name', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'file_type', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'file_size', 'type' => 'int', 'default' => ':NULL'),

        array('name' => 'thumb_path', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'source_path', 'type' => 'varchar', 'default' => ':NULL'),

        array('name' => 'file_category', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
        array('name' => 'modified', 'type' => 'datetime', 'default' => ':NOW()')
    );
	
	public static function factory($attr=array())
	{
		return new pjSupplierDocumentModel($attr);
	}
}
?>