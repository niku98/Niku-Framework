<?php
namespace App;
use system\model\Model;


class Member extends Model
{
	protected $table = 'team_members'; // Table Name
	protected $primaryKey = 'id'; // Primary key in Table
	protected $properties = ['name', 'id_code', 'class', 'birthday', 'phone_number', 'email', 'team_id']; // Another properties in Table

	public function team()
	{
		return $this->belongsTo('App\Team', 'team_id', 'id');
	}
}

?>
