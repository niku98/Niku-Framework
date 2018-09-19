<?php
namespace App;
use system\model\Model;


class Role extends Model
{
	protected $table = 'roles'; // Table Name
	protected $primaryKey = 'id'; // Primary key in Table
	protected $properties = ['name', 'display_name', 'description', 'created_at', 'updated_at']; // Another properties in Table
}

?>
