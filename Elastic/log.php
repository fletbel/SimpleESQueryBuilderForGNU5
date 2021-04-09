<?php
/**
 * @author : 김중권
 * @date   : 2020-12-17
 *
 * 엘라스틱서치 로그.
 * MAX_FILE_SIZE에 의한 로그파일 번호기능 필요.
 */
class Elastic_log extends ElasticSearch
{
    /**
     * 로그파일 이름
     *
     * @var string
     */
    protected $__fileName;

    /**
     * 로그파일 경로
     *
     * @var string
     */
    protected $__filePath;

    /**
     * 로그파일 확장자
     *
     * @var string
     */
    protected $__file_ext;

	/**
	 * 로그 입력 가능한지 아닌지
	 *
	 * @var bool
	 */
	protected $_enabled = TRUE;
    
	/**
	 * 파일권한
	 *
	 * @var	int
	 */
	protected $_file_permissions = 0644;

	/**
	 * 로그 레벨
	 *
	 * @var array
	 */
	protected $_levels = array('ERROR' => 1, 'DEBUG' => 2, 'INFO' => 3, 'ALL' => 4);

	/**
	 * mbstring func_overload 플래그값
	 *
	 * @var	bool
	 */
	protected static $func_overload;

	/**
	 * 로그 threshold 레벨 array
	 *
	 * @var array
	 */
	protected $_threshold_array = array();

	/**
	 * 로그 threshold 기본값
	 *
	 * @var string
	 */
	protected $_threshold = "debug";

    public function __construct() {
		$config = Elastic_config::$config;
        isset(self::$func_overload) OR self::$func_overload = (extension_loaded('mbstring') && ini_get('mbstring.func_overload'));
        
		$this->_log_path = $_SERVER["DOCUMENT_ROOT"] . "/log/elasticsearch/";
		$this->_file_ext = "php";
        $this->logType = ELASTIC_LOG_TYPE;

		file_exists($this->_log_path) OR mkdir($this->_log_path, 0755, TRUE);

		if ( ! is_dir($this->_log_path) OR ! is_really_writable($this->_log_path))
		{
			$this->_enabled = false;
        }
        
		if (is_numeric($config['log_threshold']))
		{
			$this->_threshold = (int) $config['log_threshold'];
		}
		elseif (is_array($config['log_threshold']))
		{
			$this->_threshold = 0;
			$this->_threshold_array = array_flip($config['log_threshold']);
		}

		if ( ! empty($config['log_date_format']))
		{
			$this->_date_fmt = $config['log_date_format'];
		}

		if ( ! empty($config['log_file_permissions']) && is_int($config['log_file_permissions']))
		{
			$this->_file_permissions = $config['log_file_permissions'];
		}
    }
    
	/**
	 * 파일에 로그 쓰기
	 *
	 * @param string $level 로그 레벨
	 * @param string $msg 로그 메세지
	 * @return	bool
	 */
	public function write_log($level, $msg)
	{
		if ($this->_enabled === FALSE)
		{
			return FALSE;
		}

		$level = strtoupper($level);
		if (( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold))
			&& ! isset($this->_threshold_array[$this->_levels[$level]]))
		{
			// return FALSE;
		}

		$filepath = $this->_log_path.'log-'.date('Y-m-d').'.'.$this->_file_ext;
		$message = '';

		if ( ! file_exists($filepath))
		{
			$newfile = TRUE;
			if ($this->_file_ext === 'php')
			{
				$message .= "<?php defined('ELASTIC_ROOT') OR exit('No direct script access allowed'); ?>\n\n";
			}
		}

		if ( ! $fp = @fopen($filepath, 'ab'))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);
        
        $date = date("Y-m-d H:i:s");

		$message .= $this->_format_line($level, $date, $msg);

		for ($written = 0, $length = self::strlen($message); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, self::substr($message, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		if (isset($newfile) && $newfile === TRUE)
		{
			chmod($filepath, $this->_file_permissions);
		}

		return is_int($result);
    }
    
	/**
	 * 로그 메세지 포멧하는 함수. 로그 메세지 기록되는 방식을 바꾸려면 이 부분을 수정
     * 
	 * @param	string	$level  에러레벨
	 * @param	string	$date 날짜
	 * @param	string	$message 메세지
	 * @return	string
	 */
	protected function _format_line($level, $date, $message)
	{
		return $level.' - '.$date.' --> '.$message."\n";
	}

	/**
	 * Byte-safe strlen()
	 *
	 * @param	string	$str
	 * @return	int
	 */
	protected static function strlen($str)
	{
		return (self::$func_overload)
			? mb_strlen($str, '8bit')
			: strlen($str);
	}

	/**
	 * Byte-safe substr()
	 *
	 * @param	string	$str
	 * @param	int	$start
	 * @param	int	$length
	 * @return	string
	 */
	protected static function substr($str, $start, $length = NULL)
	{
		if (self::$func_overload)
		{
			isset($length) OR $length = ($start >= 0 ? self::strlen($str) - $start : -$start);
			return mb_substr($str, $start, $length, '8bit');
		}

		return isset($length)
			? substr($str, $start, $length)
			: substr($str, $start);
    }
    
    protected  function getlogType() {
        return $this->logType;
    }

    protected function logException($e) {
        $this->write_log($e->getMessage(), "error");
    }
}