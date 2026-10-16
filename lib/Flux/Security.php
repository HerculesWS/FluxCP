<?php
/**
 * Flux_Security is a helper that contain security related methods
 * For now it's just used to avoid CSRF exploits.
 */
class Flux_Security {

	/**
	 * @var Array security namespace
	 * @access private
	 */
	static private $session;

	/**
	 * Set session namespace
	 * @param array $storage ($_SESSION, $_COOKIE, etc.)
	 * @access public
	 */
	static public function setSession( &$storage )
	{
		// Create the storage if not exist yet
		if ( empty( $storage['security'] ) ) {
			$storage['security'] = array();
		}

		self::$session = &$storage['security'];

		// Generate session key (used in some special case)
		if ( !self::csrfGet('Session') ) {
			self::csrfGenerate('Session');
		}
	}

	/**
	 * Generate a token stored in session to avoid future CSRF exploit
	 * @param string $name identifier
	 * @param bool $isForm - used in a form ?
	 * @return string hash
	 */
	static public function csrfGenerate( $name, $isForm = false )
	{
		// Generate a random token
		$hash = self::generateString(64);
		self::$session[ 'CSRF_' . $name ] = $hash;

		if ( $isForm ) {
			return '<input type="hidden" name="'. $name .'" value="'. $hash .'"/>';
		}

		return $hash;
	}

	/**
	 * Get back a hash from the storage
	 * @param string $name identifier
	 * @return string hash value
	 * @access public
	 */
	static public function csrfGet( $name )
	{
		if( empty(self::$session[ 'CSRF_' . $name ]) ) {
			return FALSE;
		}
		return self::$session[ 'CSRF_' . $name ];
	}

	/**
	 * Check CSRF validity
	 * @param string $name identifier
	 * @param array $storage check : $_POST / $_GET / ect.
	 * @param string $error reference to overwrite if something to say
	 * @return bool PASS
	 * @access public
	 */
	static public function csrfValidate( $name, $storage, &$error )
	{
		// Missing session token
		if ( !isset( self::$session[ 'CSRF_' . $name ] ) ) {
			$error = Flux::message('SecurityNeedSession');
			return false;
		}

		// Missing origin token
		if ( !isset( $storage[ $name ] ) ) {
			$error = Flux::message('SecurityNeedToken');
			return false;
		}

		// Get back hash, clean up session
		$hash = self::$session[ 'CSRF_' . $name ];
		unset(self::$session[ 'CSRF_' . $name ]);

		// Invalid token
		if ( !is_string( $storage[ $name ] ) || !hash_equals( $hash, $storage[ $name ] ) ) {
			$error = Flux::message('SecuritySessionInvalid');
			return false;
		}

		// PASS
		return true;
	}

	/**
	 * Check a token without using it up, for links that carry the session
	 * token (the 'Session' one) and can be clicked more than once per page.
	 * @param string $name identifier
	 * @param array $storage check : $_GET / ect.
	 * @param string $error reference to overwrite if something to say
	 * @return bool PASS
	 * @access public
	 */
	static public function csrfCheck( $name, $storage, &$error )
	{
		if ( empty( self::$session[ 'CSRF_' . $name ] ) ) {
			$error = Flux::message('SecurityNeedSession');
			return false;
		}

		if ( !isset( $storage[ $name ] ) ) {
			$error = Flux::message('SecurityNeedToken');
			return false;
		}

		if ( !is_string( $storage[ $name ] ) || !hash_equals( self::$session[ 'CSRF_' . $name ], $storage[ $name ] ) ) {
			$error = Flux::message('SecuritySessionInvalid');
			return false;
		}

		return true;
	}

	/**
	 * Generate a random string
	 * @param integer $count output string length
	 * @param string $extra string containing custom chars
	 * @return string
	 * @access public
	 */
	public static function generateString( $count, $extra='' )
	{
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' . $extra;
		$last       = strlen($characters) - 1;
		$output     = '';

		for ($i = 0; $i < $count; ++$i) {
			$output .= $characters[random_int(0, $last)];
		}

		return $output;
	}
}
?>