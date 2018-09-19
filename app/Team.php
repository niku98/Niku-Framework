<?php
namespace App;
use system\model\Model;


class Team extends Model
{
	protected $table = 'teams'; // Table Name
	protected $primaryKey = 'id'; // Primary key in Table
	protected $properties = ['name', 'idea_file']; // Another properties in Table

	public function members()
	{
		return $this->hasMany('App\Member', 'id', 'team_id');
	}
}

?>
