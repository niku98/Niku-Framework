<?php

namespace system\responses;
use system\supporters\Request;
use system\supporters\File;
/**
 * Response class | Process response data
 */
class Response
{
	private $statusCode = 200;
	private $version = '1.0';
	private $header = array();
	private $protocol = 'HTTP';
	private $body;

	public static $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',                                     // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    );

	public function __toString()
	{
		return $this->prepare()->getBody();
	}

	public function prepare(){
		$this->loadHeaders()->loadStatus();
		return $this;
	}

	public function header(string $key, string $value){
		$this->header[$key] = $value;
		return $this;
	}

	public function getHeader($key){
		return isset($this->header[$key]) ? $this->header[$key] : null;
	}

	public function loadHeaders(){
		foreach ($this->header as $key => $value) {
			header($key.': '.$value);
		}
		return $this;
	}

	public function body(string $body){
		$this->body = $body;
		return $this;
	}

	public function getBody(){
		return $this->body;
	}

	public function status(int $code){
		if(empty(self::$statusTexts[$code]))
			return false;

		$this->statusCode = $code;
		return $this;
	}

	public function version(string $version){
		$this->version = $version;
		return $this;
	}

	public function protocol(string $protocol){
		$this->protocol = $protocol;
		return $this;
	}

	public function loadStatus(){
		$content = $this->protocol.'/'.$this->version.' '.$this->statusCode.' '.self::$statusTexts[$this->statusCode];
		header($content);
		return $this;
	}

	public function contentType($contents){
		if(is_array($contents)){
			$content = array();
			foreach ($contents as $key => $value) {
				if(!empty($key) && !is_numeric($key))
					$content[] .= $key.'='.$value;
				else{
					$content[] = $value;
				}
			}
			$content = implode($content, ';');
		}else{
			$content = $contents;
		}

		return $this->header('Content-Type', $content);
	}

	public function cookie(){
		if(is_array($contents)){
			$content = array();
			foreach ($contents as $key => $value) {
				if(!empty($key) && !is_numeric($key))
					$content[] .= $key.'='.$value;
				else{
					$content[] = $value;
				}
			}
			$content = implode($content, ';');
		}else{
			$content = $contents;
		}

		return $this->header('Set-Cookie', $content);
	}

	/*----------------------------------------
	QUICK METHODS
	----------------------------------------*/
	public function file(File $file){
		$this->contentType($file->getMimeType())->body($file->getContent());
		return $this;
	}

	public function json($data){
		$this->contentType('application/json')->body(json_encode($data));
		return $this;
	}

	public function download($data){
		if(is_object($data) && strpos(get_class($data), 'File') !== false){
			return $this->downloadWithFile($data);
		}else{
			return $this->downloadWithBody(...func_get_args());
		}
	}

	public function downloadWithFile(File $file){
		return $this->downloadWithBody($file->getContent(), $file->getFullName());
	}

	public function downloadWithBody(string $data, string $name)
	{
		$this->header('Content-Disposition', 'attachment; filename="'.$name.'"')
		->contentType([
			'application/download',
			'application/octet-stream'
			])->body($data);
		return $this;
	}
}


 ?>
