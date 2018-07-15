<?php
namespace system\supporters;
use system\patterns\Singleton;
/**
 * Time class
 */
class Time extends Singleton
{
	private $time;
	private $from_time;
	private $to_time;

	protected function __construct(string $time, $from_time = '', $to_time = '')
	{
		$this->time = $time;

		if(!empty($from_time))
			$this->from($from_time);

			if(!empty($to_time))
				$this->to($to_time);

		return $this;
	}

	public function countFrom($time){
		$this->from($time)->to(time())->count();
	}

	public function from($time)
	{
		if(gettype($time) === 'integer')
			$this->from_time = $time;
		else
			$this->from_time = strtotime($time);

		return $this;
	}

	public function to($time)
	{
		if(gettype($time) === 'integer')
			$this->to_time = $time;
		else
			$this->to_time = strtotime($time);

		return $this;
	}

	public function count()
	{
		$seconds = $this->to_time - $this->from_time;

		if($seconds >= 60 * 60 * 24 * 30 * 365){
			$year = $seconds / 365 / 30 / 24 / 60 / 60;
			$year = (int)$year;
			return $year == 1 ? 'A year ago' : $year.' years ago';
		}
		else if($seconds >= 60 * 60 * 24 * 30){
			$months = $seconds / 30 / 24 / 60 / 60;
			$months = (int)$months;
			return $months == 1 ? 'A month ago' : $months.' months ago';
		}
		else if($seconds >= 60 * 60 * 24){
			$days = $seconds / 24 / 60 / 60;
			$days = (int)$days;
			return $days == 1 ? 'A day ago' : $days.' days ago';
		}
		else if($seconds >= 60 * 60){
			$hours = $seconds / 60 / 60;
			$hours = (int)$hours;
			return $hours == 1 ? 'A hour ago' : $hours.' hours ago';
		}
		else if($seconds >= 60){
			$minutes = $seconds / 60;
			$minutes = (int)$minutes;
			return $minutes == 1 ? 'A minute ago' : $minutes.' minutes ago';
		}else{
			return $seconds == 1 ? 'A second ago' : $seconds.' seconds ago';
		}
	}

	public function current(string $type = ''){
		switch ($type) {
			case 'mysql':
				return $this->mysql();
				break;
			case 'locale':
				return $this->locale();

			default:
				return time();
				break;
		}
	}

	public function mysql($time = ''){
		if(empty($time))
			return date('Y-m-d H:i:s', time());

		if(!is_numeric($time))
			return date('Y-m-d H:i:s', strtotime($time));

		return date('Y-m-d H:i:s', $time);
	}

	public function locale(string $locale = ''){
		$locale = empty($locale) ? app()->locale() : $locale;
	}

	public function format(string $format, $time)
	{
		if(!is_numeric($time)){
			$time = strtotime($time);
		}

		return date($format, $time);
	}
}



 ?>
