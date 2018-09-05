<?php
namespace system\supporters;
use Redirect;
use Session;
use AppException;
use Database;

/**
 *
 */
class Validator
{
	private $data;
	private $rules;
	private $messages;

	private $allRules = [
		'required' => [
			'action' => 'validateRequired',
			'message' => '%s is required!',
			'has_val' => false,
		],
		'required_if' => [
			'action' => 'validateRequiredIf',
			'message' => '%s is required!',
			'has_val' => true,
		],
		'email'=> [
			'action' => 'validateEmail',
			'message' => '%s must be an email!',
			'has_val' => false,
		],
		'url' => [
			'action' => 'validateUrl',
			'message' => '%s must be an url!',
			'has_val' => false,
		],
		'date' => [
			'action' => 'validateDate',
			'message' => '%s must be date format',
			'has_val' => false,
		],
		'time' => [
			'action' => 'validateTime',
			'message' => '%s must be time format',
			'has_val' => false,
		],
		'datetime' => [
			'action' => 'validateDateTime',
			'message' => '%s must be datetime format!',
			'has_val' => false,
		],
		'numeric' => [
			'action' => 'validateNumeric',
			'message' => '%s must be a number!',
			'has_val' => false,
		],
		'min' => [
			'action' => 'validateMin',
			'message' => '%s must larger than or equal to %d!',
			'has_val' => true,
		],
		'max' => [
			'action' => 'validateMax',
			'message' => '%s must smaller than or equal to %d!',
			'has_val' => true,
		],
		'minLength' => [
			'action' => 'validateMinLength',
			'message' => '%s\'s length must larger than or equal to %d!',
			'has_val' => true,
		],
		'maxLength' => [
			'action' => 'validateMaxLength',
			'message' => '%s\'s length must smaller than or equal to %d!',
			'has_val' => true,
		],
		'unique' => [
			'action' => 'validateUnique',
			'message' => '%s is existed!',
			'has_val' => true,
		],
		'same' => [
			'action' => 'validateSame',
			'message' => '%s must be same %s',
			'has_val' => true,
		],
		'nullable' => [
			'action' => 'setNullable',
			'has_val' => false
		],

		'file' => [
			'action' => 'validateFile',
			'has_val' => false
		],

		'array' => [
			'action' => 'validateArray',
			'has_val' => false
		],
	];

	public function __construct(array $data_to_check = array(), array $rules = array(), array $custom_messages = array())
	{
		$this->data = $data_to_check;
		$this->rules = $rules;
		$this->messages = $custom_messages;

		if($this->canValidate()){
			$this->validateData();
		}

		return $this;
	}

	public function data(array $data){
		$this->data = $data;

		if($this->canValidate())
			$this->validateData();

		return $this;
	}

	public function rules(array $rules){
		$this->rules = $rules;

		if($this->canValidate())
			$this->validateData();

		return $this;
	}

	/*----------------------------------
	Validate Methods
	----------------------------------*/
	private function canValidate(){
		return empty($this->data) || empty($this->rules) ? false : true;
	}

	private function findInData($path){
		return DotPath::findInArray($this->data, $path);
	}

	private function validateData(){
		$message = array();
		foreach ($this->rules as $field => $rule) {
			$rules = $this->parseRule($rule);

			foreach ($rules as $rulee) {
				if($rulee['name'] == 'nullable'){
					$checker = $this->setNullable($field);
				}else{
					if(empty($this->findInData($field)) && $this->isNull($field)){
						$checker = true;
					}else if(!empty($this->findInData($field))){
						$checker = $this->validateWithRule($field, $rulee);
					}else{
						$checker = false;
					}
				}


				if($checker === false){
					$message[] = $this->getErrorMessage($field, $rulee);
					if($rulee['name'] == 'required')
						break;
				}
			}
		}
		if(!empty($message)){
			redirect()->back()->with('validate_errors', $message)->go();
		}
	}

	private function parseRule(string $rules){
		$list_rules = $this->getListRules($rules);
		$result = array();

		foreach($list_rules as $rule){
			$result[] = $this->getRuleNameAndValue($rule);
		}
		return $result;
	}

	private function getListRules(string $rules){
		return explode('|', $rules);
	}

	private function getRuleNameAndValue(string $rule){
		$result = array();
		$values = explode(':', $rule);

		if(count($values) < 1)
			return false;

		$result['name'] = $values[0];

		if(!isset($values[1]))
			return $result;

		$values = explode(',', $values[1]);

		foreach($values as $val){
			$result['value'][] = $val;
		}
		return $result;
	}

	private function ruleHasVal(string $name){
		return $this->allRules[$name]['has_val'];
	}

	private function isRuleExists($name){
		return isset($this->allRules[$name]);
	}

	private function validateWithRule(string $field, array $rule){
		$name = $rule['name'];

		if(!$this->isRuleExists($rule['name'])){
			throw new AppException("Rule [$rule[name]] is not exists!");
		}

		$method = $this->allRules[$name]['action'];

		if($this->ruleHasVal($name)){
			$values = $rule['value'];
			return $this->$method($field, $values);
		}else{
			return $this->$method($field);
		}
	}

	private function validateEmail(string $field){
		return (bool)preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $this->findInData($field));
	}

	private function validateRequired(string $key){
		return empty($this->findInData($key)) ? false : true;
	}

	private function validateRequiredIf(string $key, $other){
		if($this->validateRequired($other[0]))
			return empty($this->findInData($key)) ? false : true;
		return true;
	}

	private function setNullable(string $key){
		$this->null[$key] = true;

		return true;
	}

	private function isNull(string $key){
		return !empty($this->null[$key]);
	}

	/*Validate Date - Time*/
	private function validateTime(string $field, string $type = 'field'){
		return (bool)preg_match('/^([0-9]|[0-1][0-9]|2[0-3]):[0-5]?[0-9](:[0-5]?[0-9])?( AM| PM)?$/', empty($this->findInData($field)) ? $field : $this->findInData($field));
	}

	private function validateUrl(string $field){
		return (bool)preg_match('/^((https|http)?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', empty($this->findInData($field)) ? '' : $this->findInData($field));
	}

	private function validateDate(string $field, string $type = 'field')
	{
		$date = $type == 'field' ? $this->parseDate($this->findInData($field)) : $this->parseDate($field);
		if(count($date) !== 3)
			return false;
		return checkdate($date['month'], $date['day'], $date['year']);
	}

	private function parseDate(string $date){
		$divider = strpos($date, '-') !== false ? '-' : '/';
		$parts = explode($divider, $date);
		$date = array();
		foreach ($parts as $part) {
			if($part > 100){
				$date['year'] = $part;
			}else if($part > 12){
				$date['month'] = !empty($date['day']) ? $date['day'] : NULL;
				$date['day'] = $part;
			}else{
				if(!empty($date['day'])){
					$date['month'] = $part;
				}else{
					$date['day'] = $part;
				}
			}
		}

		return $date;
	}

	private function validateDateTime(string $field){
		$dateTime = $this->splitDateTime($this->findInData($field));
		if(!$dateTime)
			return false;

		return $this->validateDate($dateTime['date'], 'date') && $this->validateTime($dateTime['time'], 'time');
	}

	private function splitDateTime(string $dateTime){
		$parts = explode(' ', $dateTime);
		if(count($parts) == 2){
			return array(
				'date' => $parts[0],
				'time' => $parts[1]
			);
		}else if(count($parts) == 3){
			return array(
				'date' => $parts[0],
				'time' => $parts[1].' '.$parts[2]
			);
		}
		return false;
	}

	/*Validate Number*/
	private function validateMax(string $field, $max){
		if($this->findInData($field) !== null || !$this->validateNumeric($field))
			return false;

		return $this->findInData($field) <= $max[0];
	}

	private function validateMin(string $field, $min){
		if($this->findInData($field) !== null || !$this->validateNumeric($field))
			return false;

		return $this->findInData($field) >= $min[0];
	}

	private function validateNumeric(string $field){
		if($this->findInData($field) !== null)
			return false;

		return is_numeric($this->findInData($field));
	}
	/*End Validate Number*/

	private function validateSame(string $root, $vals){
		if($this->findInData($root) !== null)
			return false;

		foreach ($vals as $val) {
			if($this->findInData($root) !== $this->findInData($val))
				return false;
		}

		return true;
	}

	private function validateMinLength(string $field, $min){
		return strlen($this->findInData($field)) >= $min[0];
	}

	private function validateMaxLength(string $field, $max){
		return strlen($this->findInData($field)) >= $max[0];
	}

	private function validateUnique(string $field, array $models){
		foreach ($models as $model) {
			$tableAndCol = explode('.', $model);
			if(count($tableAndCol) < 2)
				throw new AppException('You must use Unique Validate with this format "unique:table_1.column[, table_2.column2, ...]"');

			$table = $tableAndCol[0];
			$column = $tableAndCol[1];

			$existed = Database::table($table)->where($column, $this->findInData($field))->limit(1)->count();

			return $existed > 0 ? false : true;
		}
	}

	private function validateFile($field)
	{
		if($this->findInData($field) !== null && $this->findInData($field)['tmp_name'] != null){
			return true;
		}

		return false;
	}

	private function validateArray(string $field)
	{
		return is_array($this->findInData($field));
	}

	/*---------------------------------
	Messages processing Methods
	---------------------------------*/
	private function getErrorMessage(string $field, array $rule){
		$format = $this->getMessageFormat($field, $rule['name']);
		$data[] = $field;
		if($this->ruleHasVal($rule['name'])){
			array_push($data, ...$rule['value']);
		}
		return sprintf($format, ...$data);
	}

	private function getMessageFormat(string $field, string $name){
		if($this->hasCustomMessageFormat($field, $name)){
			return $this->getCustomMessageFormat($field, $name);
		}

		return $this->getDefaultMessageFormat($name);
	}

	private function hasCustomMessageFormat(string $field, string $name){
		return !empty($this->messages[$field.'.'.$name]);
	}

	private function getCustomMessageFormat(string $field, string $name){
	 	return $this->messages[$field.'.'.$name];
	}

	private function getDefaultMessageFormat(string $name){
	 	return $this->allRules[$name]['message'];
	}
}


 ?>
