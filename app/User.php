<?php
namespace App;
use System\Model\Model;
use System\App\Authentication\Authenticate;
/**
 * User model
 */
class User extends Model
{
	protected $primaryKey = 'id';
	protected $table = 'users';

	protected $properties = ['username', 'passsword', 'name', 'auth', 'created_at', 'updated_at'];

	use Authenticate;

	public function roles(){
		return $this->belongsToMany('App\Role', 'user_role', 'user_id', 'role_id');
	}
}

 ?>
