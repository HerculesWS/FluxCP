<?php
/**
 * Convenient way to log information to files.
 */
class Flux_LogFile {
	/**
	 * File handler for open log file.
	 *
	 * @access public
	 * @var resource
	 */
	private $fp;

	/**
	 * Log file name.
	 *
	 * @access public
	 * @var string
	 */
	public $filename;

	/**
	 * Date format used to indicate when the action was logged.
	 *
	 * @access public
	 * @var string
	 */
	public $dateFormat = '[Y-m-d H:i:s] ';

	/**
	 * Create new LogFile instance.
	 *
	 * @param string $filename
	 * @param string $mode (see: http://www.php.net/fopen)
	 * @access public
	 */
	public function __construct($filename, $mode = 'a')
	{
		$this->filename  = "$filename.php";
		$isNewFile       = !file_exists($this->filename);

		if ($isNewFile) {
			touch($this->filename);
			chmod($this->filename, 0600);
		}

		$this->fp = fopen($this->filename, 'a');
		if ($isNewFile) {
			fputs($this->fp, "<?php exit('Forbidden'); ?>\n");
		}
	}

	/**
	 * Close file handle.
	 */
	public function __destruct()
	{
		if ($this->fp) {
			fclose($this->fp);
		}
	}

	/**
	 * Write a line to the log file.
	 *
	 * @param string $format
	 * @param string $var, ...
	 * @access public
	 */
	public function puts()
	{
		$args = func_get_args();
		if (count($args) > 0) {
			$format = array_shift($args);

			// With no arguments the text is taken as it is, it may hold untrusted values.
			$line = count($args) ? vsprintf($format, $args) : $format;

			// Line breaks and other control characters in the values could forge log lines.
			$line = preg_replace_callback('/[\x00-\x1f\x7f]/', function ($m) {
				return sprintf('\\x%02x', ord($m[0]));
			}, $line);

			return fwrite($this->fp, date($this->dateFormat).$line."\n");
		}
		else {
			return false;
		}
	}
}
?>