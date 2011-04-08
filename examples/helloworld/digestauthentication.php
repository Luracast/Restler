<?php
/**
 * Digest Authentication Scheme Example for Restler Framework
 * @category   Framework
 * @package    restler
 * @subpackage auth
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class DigestAuthentication implements iAuthenticate
{
	public $realm = 'Restricted API';
	public static $user;
	public $restler;

	public function isAuthenticated()
	{
		//user => password hardcoded for convenience
		$users = array('admin' => 'mypass', 'guest' => 'guest');

		if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$this->realm.'",qop="auth",nonce="'.
			uniqid().'",opaque="'.md5($this->realm).'"');
			die('Digest Authorisation Required');
		}

		// analyze the PHP_AUTH_DIGEST variable
		if (!($data = DigestAuthentication::http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
		!isset($users[$data['username']]))
		{
			throw new RestException(401, 'Wrong Credentials!');
		}


		// generate the valid response
		$A1 = md5($data['username'] . ':' . $this->realm . ':' . $users[$data['username']]);
		$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
		$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

		if ($data['response'] != $valid_response)
		{
			throw new RestException(401, 'Wrong Credentials!');
		}
		// ok, valid username & password
		DigestAuthentication::$user=$data['username'];
		return true;
	}

	/**
	 * Logs user out of the digest authentication by bringing the login dialog again
	 * ignore the dialog to logout
	 *
	 * @url GET /user/login
	 * @url GET /user/logout
	 */
	public function logout()
	{
		header('HTTP/1.1 401 Unauthorized');
		header('WWW-Authenticate: Digest realm="'.$this->realm.'",qop="auth",nonce="'.
		uniqid().'",opaque="'.md5($this->realm).'"');
		die('Digest Authorisation Required');
	}


	// function to parse the http auth header
	private function http_digest_parse($txt)
	{
		// protect against missing data
		$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
		$data = array();
		$keys = implode('|', array_keys($needed_parts));

		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

		foreach ($matches as $m) {
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($needed_parts[$m[1]]);
		}

		return $needed_parts ? false : $data;
	}
}