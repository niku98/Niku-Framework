<?php
namespace App;
use system\model\Model;
/**
 * User model
 */
class User extends Model
{
	protected $primaryKey = 'id';
	protected $table = 'users';

	protected $properties = ['username', 'passsword', 'name', 'auth', 'created_at', 'updated_at'];

	public function save(){
		$primaryKey = $this->primaryKey;
		$this->changeTable();
		if(!empty($this->data[$primaryKey])){
			self::$db->where($primaryKey, $this->$primaryKey)->update($this->data);
			if(self::$db->affected_rows() != 0)
				return true;
			return false;

		}else{
			$this->salt_token = rand_token(12);
			$this->password = md5($this->password).md5($this->salt_token);

			self::$db->insert($this->data);
			$this->$primaryKey = self::$db->insert_id();
			if($this->$primaryKey != 0)
				return true;
		}
		return false;
	}

	public function roles(){
		return $this->belongsToMany('App\Role', 'role_user', 'user_id', 'role_id');
	}
}

 ?>
