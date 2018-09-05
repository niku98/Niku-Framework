<?php
namespace App;
use system\model\Model;
/**
 * User model
 */
class User extends Model
{
	protected $identification = 'id';
	protected $table = 'users';

	protected $properties = ['username', 'passsword', 'displayname', 'age', 'role', 'salt_token'];

	public function save(){
		$identification = $this->identification;
		$this->changeTable();
		if(!empty($this->data[$identification])){
			self::$db->where($identification, $this->$identification)->update($this->data);
			if(self::$db->affected_rows() != 0)
				return true;
			return false;

		}else{
			$this->salt_token = rand_token(12);
			$this->password = md5($this->password).md5($this->salt_token);

			self::$db->insert($this->data);
			$this->$identification = self::$db->insert_id();
			if($this->$identification != 0)
				return true;
		}
		return false;
	}

	public function roles(){
		return $this->belongsToMany('App\Role', 'users_roles', 'role_id', 'user_id');
	}
}

 ?>
