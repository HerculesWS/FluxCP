<?php
if (!defined('FLUX_ROOT')) exit;

if (!function_exists('fluxEmailLayout')) {
	/**
	 * Shared e-mail layout. Uses a table and inline styles on purpose, because mail clients ignore most modern CSS.
	 *
	 * Options: title, preheader, intro (HTML), details (label => HTML value), button (array(label, link)),
	 * bodyHtml (HTML), note (HTML). Values may contain {Placeholders}; Flux_Mailer fills them in afterwards.
	 */
	function fluxEmailLayout(array $o)
	{
		$siteTitle = (string)Flux::config('SiteTitle');
		$title     = isset($o['title']) ? (string)$o['title'] : $siteTitle;
		$accent    = '#4083c6';
		$muted     = '#6b7785';
		$line      = '#e3e8ee';
		$esc       = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };

		$html  = '<!DOCTYPE html>' . "\n";
		$html .= '<html lang="en"><head><meta charset="utf-8">';
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
		$html .= '<meta name="color-scheme" content="light"><meta name="supported-color-schemes" content="light">';
		$html .= '<title>' . $esc($title) . '</title></head>';
		$html .= '<body style="margin:0;padding:0;background:#f1f4f8;">';

		if (!empty($o['preheader'])) {
			$html .= '<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:#f1f4f8;">' . $esc($o['preheader']) . '</div>';
		}

		$html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f4f8;"><tr><td align="center" style="padding:24px 12px;">';
		$html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border:1px solid ' . $line . ';border-radius:10px;font-family:-apple-system,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;font-size:15px;line-height:1.55;color:#1f2933;">';

		// Header band.
		$html .= '<tr><td style="background:' . $accent . ';color:#ffffff;padding:18px 28px;border-radius:10px 10px 0 0;font-size:14px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;">' . $esc($siteTitle) . '</td></tr>';

		$html .= '<tr><td style="padding:28px;">';
		$html .= '<h1 style="margin:0 0 16px;font-size:22px;line-height:1.3;color:#1f2933;">' . $esc($title) . '</h1>';

		if (!empty($o['intro'])) {
			$html .= '<p style="margin:0 0 16px;">' . $o['intro'] . '</p>';
		}
		if (!empty($o['bodyHtml'])) {
			$html .= $o['bodyHtml'];
		}

		if (!empty($o['details'])) {
			$html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;border:1px solid ' . $line . ';border-radius:8px;">';
			$i = 0;
			$n = count($o['details']);
			foreach ($o['details'] as $label => $value) {
				$border = (++$i < $n) ? 'border-bottom:1px solid ' . $line . ';' : '';
				$html .= '<tr><td style="padding:10px 14px;' . $border . 'color:' . $muted . ';font-size:13px;width:38%;">' . $esc($label) . '</td>';
				$html .= '<td style="padding:10px 14px;' . $border . 'font-weight:600;word-break:break-all;">' . $value . '</td></tr>';
			}
			$html .= '</table>';
		}

		if (!empty($o['button'])) {
			list($label, $link) = $o['button'];
			$html .= '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 20px;"><tr><td style="background:' . $accent . ';border-radius:8px;">';
			$html .= '<a href="' . $link . '" style="display:inline-block;padding:12px 24px;color:#ffffff;font-weight:600;text-decoration:none;">' . $esc($label) . '</a>';
			$html .= '</td></tr></table>';
			$html .= '<p style="margin:0 0 16px;font-size:13px;color:' . $muted . ';">If the button does not work, copy this link into your browser:<br>';
			$html .= '<a href="' . $link . '" style="color:' . $accent . ';word-break:break-all;">' . $link . '</a></p>';
		}

		if (!empty($o['note'])) {
			$html .= '<p style="margin:0 0 16px;">' . $o['note'] . '</p>';
		}

		$html .= '</td></tr>';

		$html .= '<tr><td style="padding:16px 28px;border-top:1px solid ' . $line . ';font-size:12px;color:' . $muted . ';">';
		$html .= 'This is an automated e-mail from ' . $esc($siteTitle) . ', please do not reply to this address.';
		$html .= '</td></tr>';

		$html .= '</table></td></tr></table></body></html>';

		return $html;
	}
}
