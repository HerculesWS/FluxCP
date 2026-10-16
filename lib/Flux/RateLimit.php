<?php
if (!defined('FLUX_ROOT')) exit;

/**
 * Small file based counter used to slow down repeated requests, such as
 * password guesses or reset e-mail requests. State is kept in data/tmp.
 */
class Flux_RateLimit {
	/**
	 * Whether the given key already used up its attempts in the time window.
	 *
	 * @param string $bucket Name of the action being limited.
	 * @param string $key Who is being limited (an IP address, a username, ...).
	 * @param int $max Number of attempts allowed. 0 or less turns the limit off.
	 * @param int $window Time window in seconds.
	 * @return bool
	 */
	public static function isLimited($bucket, $key, $max, $window)
	{
		if ($max <= 0) {
			return false;
		}

		$file = self::file($bucket, $key);
		$data = is_file($file) ? json_decode((string)@file_get_contents($file), true) : null;

		return count(self::recent($data, $window)) >= $max;
	}

	/**
	 * Count one more attempt for the given key.
	 */
	public static function hit($bucket, $key, $window)
	{
		$fp = @fopen(self::file($bucket, $key), 'c+');
		if (!$fp) {
			return;
		}

		if (flock($fp, LOCK_EX)) {
			$data   = self::recent(json_decode((string)stream_get_contents($fp), true), $window);
			$data[] = time();

			ftruncate($fp, 0);
			rewind($fp);
			fwrite($fp, json_encode($data));
			fflush($fp);
			flock($fp, LOCK_UN);
		}
		fclose($fp);

		// Now and then, clean up counters nobody has touched for a day.
		if (mt_rand(1, 200) === 1) {
			foreach ((array)glob(FLUX_DATA_DIR.'/tmp/ratelimit_*.rl') as $old) {
				if (filemtime($old) < time() - 86400) {
					@unlink($old);
				}
			}
		}
	}

	/**
	 * Forget the attempts for the given key.
	 */
	public static function clear($bucket, $key)
	{
		$file = self::file($bucket, $key);
		if (is_file($file)) {
			@unlink($file);
		}
	}

	private static function file($bucket, $key)
	{
		return FLUX_DATA_DIR.'/tmp/ratelimit_'.sha1($bucket.'|'.$key).'.rl';
	}

	private static function recent($data, $window)
	{
		$data = is_array($data) ? $data : array();
		$from = time() - $window;

		return array_values(array_filter($data, function ($time) use ($from) {
			return $time > $from;
		}));
	}
}
?>
