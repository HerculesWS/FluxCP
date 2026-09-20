<?php
/**
 * Wraps the reCAPTCHA client library with Flux's config values, so the
 * v2/v3 handling and score threshold check only live in one place.
 */
class Flux_ReCaptcha {
	/**
	 * Whether the configured reCAPTCHA version renders a visible widget.
	 * v3 is invisible, so callers can use this to skip drawing a labeled
	 * form row around it.
	 *
	 * @return bool
	 */
	public static function isVisible()
	{
		return strtolower(Flux::config('ReCaptchaVersion')) !== 'v3';
	}

	/**
	 * Build the HTML for the reCAPTCHA widget, for either v2 (visible
	 * checkbox) or v3 (invisible, runs on form submit).
	 *
	 * @param string $fieldId id to give the security code field/container,
	 *                        also used as the v3 execute callback name.
	 * @return string HTML markup, including the api.js <script> tag.
	 */
	public static function widget($fieldId)
	{
		$siteKey = Flux::config('ReCaptchaPublicKey');

		if (strtolower(Flux::config('ReCaptchaVersion')) === 'v3') {
			$action = Flux::config('ReCaptchaV3Action');

			// json_encode(), not htmlspecialchars(), for values used inside
			// the <script> block -- these are JS string literals, not HTML text.
			// JSON_HEX_TAG keeps a stray "</script>" in a config value from
			// closing the block early.
			$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP;

			return '<script src="https://www.google.com/recaptcha/api.js?render='.htmlspecialchars($siteKey).'"></script>
			<input type="hidden" name="g-recaptcha-response" id="'.htmlspecialchars($fieldId).'" />
			<script>
				grecaptcha.ready(function() {
					grecaptcha.execute('.json_encode((string)$siteKey, $jsonFlags).', {action: '.json_encode((string)$action, $jsonFlags).'}).then(function(token) {
						document.getElementById('.json_encode((string)$fieldId, $jsonFlags).').value = token;
					});
				});
			</script>';
		}

		return '<script src="https://www.google.com/recaptcha/api.js"></script>
		<div class="g-recaptcha" data-sitekey="'.htmlspecialchars($siteKey).'" data-theme="'.htmlspecialchars(Flux::config('ReCaptchaTheme')).'"></div>';
	}

	/**
	 * Verify the g-recaptcha-response token submitted with the current
	 * POST request.
	 *
	 * @return bool true if the submission passes (v2 success, or v3
	 *              success with a score at/above the configured threshold).
	 */
	public static function verify()
	{
		$response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : null;

		if (!$response) {
			return false;
		}

		require_once 'recaptcha/recaptchalib.php';

		$recaptcha = new ReCaptcha(Flux::config('ReCaptchaPrivateKey'));
		$result    = $recaptcha->verifyResponse($_SERVER['REMOTE_ADDR'], $response);

		if (!$result->success) {
			return false;
		}

		if (strtolower(Flux::config('ReCaptchaVersion')) === 'v3') {
			$threshold = Flux::config('ReCaptchaV3Threshold');
			$threshold = ($threshold === null || $threshold === '') ? 0.5 : (float)$threshold;

			if ($result->score === null || $result->score < $threshold) {
				return false;
			}

			// Guard against a token minted for a different action/form being replayed here.
			if ($result->action !== null && $result->action !== Flux::config('ReCaptchaV3Action')) {
				return false;
			}
		}

		return true;
	}
}
