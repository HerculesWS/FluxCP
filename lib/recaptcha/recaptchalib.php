<?php
/**
 * PHP client for Google reCAPTCHA (v2 checkbox and v3 score-based).
 *
 * Talks to the classic siteverify endpoint, which both reCAPTCHA v2 and
 * v3 still use:
 *    https://developers.google.com/recaptcha/docs/verify
 *
 * @copyright Copyright (c) 2014, Google Inc.
 * @link      https://developers.google.com/recaptcha
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Result of ReCaptcha::verifyResponse().
 */
class ReCaptchaResponse
{
	public $success = false;
	public $score = null;
	public $action = null;
	public $errorCodes = array();
}

class ReCaptcha
{
	const SITE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

	private $secret;

	/**
	 * @param string $secret shared secret between site and reCAPTCHA server.
	 */
	public function __construct($secret)
	{
		if ($secret == null || $secret == '') {
			throw new InvalidArgumentException(
				'To use reCAPTCHA you must get an API key from https://www.google.com/recaptcha/admin'
			);
		}

		$this->secret = $secret;
	}

	/**
	 * Submits an HTTP POST to the reCAPTCHA siteverify endpoint.
	 *
	 * @param array $data array of parameters to be sent.
	 *
	 * @return string|false raw response body, or false on failure.
	 */
	private function submitHttpPost($data)
	{
		$query = http_build_query($data, '', '&');

		if (function_exists('curl_init')) {
			$ch = curl_init(self::SITE_VERIFY_URL);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$response = curl_exec($ch);
			curl_close($ch);

			if ($response !== false) {
				return $response;
			}
			// Fall through to file_get_contents if cURL failed.
		}

		$context = stream_context_create(array(
			'http' => array(
				'method'  => 'POST',
				'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
				'content' => $query,
				'timeout' => 10,
			),
		));

		return @file_get_contents(self::SITE_VERIFY_URL, false, $context);
	}

	/**
	 * Calls the reCAPTCHA siteverify API to verify whether the user passes
	 * the CAPTCHA test. Works for both v2 and v3 tokens; the caller is
	 * responsible for checking ->score against a threshold for v3.
	 *
	 * @param string $remoteIp IP address of end user.
	 * @param string $response g-recaptcha-response token from the client.
	 *
	 * @return ReCaptchaResponse
	 */
	public function verifyResponse($remoteIp, $response)
	{
		$recaptchaResponse = new ReCaptchaResponse();

		if ($response == null || strlen($response) === 0) {
			$recaptchaResponse->success = false;
			$recaptchaResponse->errorCodes = array('missing-input-response');
			return $recaptchaResponse;
		}

		$body = $this->submitHttpPost(array(
			'secret'   => $this->secret,
			'remoteip' => $remoteIp,
			'response' => $response,
		));

		if ($body === false) {
			$recaptchaResponse->success = false;
			$recaptchaResponse->errorCodes = array('connection-failed');
			return $recaptchaResponse;
		}

		$answers = json_decode($body, true);

		if (!is_array($answers)) {
			$recaptchaResponse->success = false;
			$recaptchaResponse->errorCodes = array('invalid-json');
			return $recaptchaResponse;
		}

		$recaptchaResponse->success = !empty($answers['success']);

		if (isset($answers['score'])) {
			$recaptchaResponse->score = (float)$answers['score'];
		}

		if (isset($answers['action'])) {
			$recaptchaResponse->action = $answers['action'];
		}

		if (!empty($answers['error-codes'])) {
			$recaptchaResponse->errorCodes = $answers['error-codes'];
		}

		return $recaptchaResponse;
	}
}
