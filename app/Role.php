<?php
namespace App;
use system\model\Model;


class Role extends Model
{
	protected $table = 'role'; // Table Name
	protected $identification = 'id'; // Primary key in Table
	protected $properties = ['something']; // Another properties in Table
}

?>
