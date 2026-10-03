<?php
if (!defined('FLUX_ROOT')) exit;

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Flux Control Panel for Hercules: Install &amp; Update</title>
		<style>
			:root {
				color-scheme: light dark;
				--bg: #f1f4f8;
				--card: #fff;
				--text: #1f2933;
				--muted: #6b7785;
				--line: #dde3ea;
				--accent: #4083c6;
				--accent-text: #fff;
				--ok: #1a7f45;
				--ok-bg: #e3f5ea;
				--bad: #b42318;
				--bad-bg: #fde8e6;
				--warn-bg: #fff4d6;
			}

			@media (prefers-color-scheme: dark) {
				:root {
					--bg: #14181d;
					--card: #1d232b;
					--text: #e6ebf0;
					--muted: #9aa6b2;
					--line: #2f3944;
					--accent: #5a9ee0;
					--accent-text: #0e1620;
					--ok: #55c987;
					--ok-bg: #173424;
					--bad: #ff8a80;
					--bad-bg: #3d1f1c;
					--warn-bg: #3a3118;
				}
			}

			*, *::before, *::after {
				box-sizing: border-box;
			}

			body {
				margin: 0;
				padding: 24px 16px;
				font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
				font-size: 15px;
				line-height: 1.5;
				color: var(--text);
				background: var(--bg);
			}

			.installer {
				display: block;
				max-width: 880px;
				margin: 0 auto;
			}

			.installer-head {
				margin-bottom: 20px;
			}

			.site-title {
				margin: 0;
				font-size: 13px;
				font-weight: 600;
				letter-spacing: .06em;
				text-transform: uppercase;
				color: var(--muted);
			}

			h1 {
				margin: 2px 0 0;
				font-size: 28px;
				line-height: 1.2;
			}

			h2, h3, h4 {
				margin: 0;
				line-height: 1.3;
			}

			h3 {
				font-size: 18px;
			}

			h4 {
				font-size: 15px;
			}

			p {
				margin: 0 0 12px;
			}

			a {
				color: var(--accent);
			}

			code {
				font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
				font-size: 13px;
				overflow-wrap: anywhere;
			}

			#content {
				padding: 0;
			}

			.card {
				margin: 0 0 20px;
				padding: 20px;
				background: var(--card);
				border: 1px solid var(--line);
				border-radius: 10px;
			}

			.card > :last-child {
				margin-bottom: 0;
			}

			.message, .error {
				margin: 0 0 16px;
				padding: 12px 14px;
				border-radius: 8px;
				border: 1px solid transparent;
			}

			.message {
				color: var(--ok);
				background: var(--ok-bg);
				border-color: var(--ok);
			}

			.error {
				color: var(--bad);
				background: var(--bad-bg);
				border-color: var(--bad);
			}

			h2.error {
				margin-bottom: 12px;
			}

			.hint, .card .lead {
				color: var(--muted);
			}

			.toolbar {
				display: flex;
				flex-wrap: wrap;
				align-items: center;
				justify-content: space-between;
				gap: 12px;
				margin: 0 0 16px;
			}

			.toolbar .toolbar-actions {
				display: flex;
				flex-wrap: wrap;
				gap: 10px;
			}

			.btn, button {
				display: inline-block;
				padding: 9px 16px;
				font: inherit;
				font-weight: 600;
				line-height: 1.3;
				color: var(--text);
				text-decoration: none;
				background: transparent;
				border: 1px solid var(--line);
				border-radius: 8px;
				cursor: pointer;
			}

			.btn:hover, button:hover {
				border-color: var(--accent);
			}

			.btn-primary, button.btn-primary {
				color: var(--accent-text);
				background: var(--accent);
				border-color: var(--accent);
			}

			.btn-primary:hover, button.btn-primary:hover {
				filter: brightness(1.08);
			}

			.form-stack {
				display: flex;
				flex-direction: column;
				gap: 16px;
				max-width: 420px;
			}

			.form-row {
				display: flex;
				flex-direction: column;
				gap: 6px;
			}

			label {
				font-weight: 600;
			}

			.input, input[type="text"], input[type="password"] {
				display: block;
				width: 100%;
				padding: 9px 12px;
				font: inherit;
				color: var(--text);
				background: var(--card);
				border: 1px solid var(--line);
				border-radius: 8px;
			}

			.input:focus, input:focus {
				outline: 2px solid var(--accent);
				outline-offset: 0;
				border-color: var(--accent);
			}

			.server-head {
				display: flex;
				flex-wrap: wrap;
				align-items: center;
				justify-content: space-between;
				gap: 12px;
				margin-bottom: 14px;
			}

			details.alt-credentials {
				margin: 0 0 16px;
				padding: 10px 14px;
				border: 1px solid var(--line);
				border-radius: 8px;
			}

			details.alt-credentials summary {
				font-weight: 600;
				cursor: pointer;
			}

			details.alt-credentials .form-stack {
				margin-top: 14px;
			}

			.sub-head {
				margin: 18px 0 8px;
				color: var(--muted);
			}

			.table-wrap {
				max-width: 100%;
				overflow-x: auto;
			}

			.schema-info {
				width: 100%;
				border-collapse: collapse;
				border-spacing: 0;
			}

			.schema-info th, .schema-info td {
				padding: 9px 12px;
				text-align: left;
				border-bottom: 1px solid var(--line);
			}

			.schema-info thead th {
				font-size: 12px;
				font-weight: 600;
				letter-spacing: .05em;
				text-transform: uppercase;
				color: var(--muted);
			}

			.schema-info tbody tr:last-child td {
				border-bottom: 0;
			}

			.schema-info.permission th {
				width: 130px;
				color: var(--muted);
				font-weight: 600;
			}

			.pill {
				display: inline-block;
				padding: 2px 10px;
				font-size: 12px;
				font-weight: 600;
				border-radius: 999px;
			}

			.uptodate {
				color: var(--ok);
				background: var(--ok-bg);
			}

			.needtoupdate {
				color: var(--bad);
				background: var(--bad-bg);
			}

			.none {
				color: var(--muted);
			}

			.schema-query {
				border-bottom: 1px dotted var(--muted);
				cursor: help;
			}

			.callout {
				margin: 16px 0 0;
				padding: 12px 14px;
				background: var(--warn-bg);
				border-radius: 8px;
			}

			.callout p:last-child {
				margin-bottom: 0;
			}

			@media (max-width: 560px) {
				body {
					padding: 16px 12px;
				}

				h1 {
					font-size: 24px;
				}

				.card {
					padding: 16px;
				}

				.toolbar .toolbar-actions, .toolbar .btn {
					width: 100%;
				}

				.toolbar .btn, .server-head button, .form-stack button {
					width: 100%;
					text-align: center;
				}

				.schema-info:not(.permission) thead {
					position: absolute;
					width: 1px;
					height: 1px;
					overflow: hidden;
					clip: rect(0 0 0 0);
				}

				.schema-info:not(.permission) tr {
					display: block;
					padding: 10px 0;
					border-bottom: 1px solid var(--line);
				}

				.schema-info:not(.permission) tbody tr:last-child {
					border-bottom: 0;
				}

				.schema-info:not(.permission) td {
					display: flex;
					justify-content: space-between;
					gap: 12px;
					padding: 3px 0;
					border-bottom: 0;
				}

				.schema-info:not(.permission) td:nth-child(2)::before {
					content: "Latest";
					color: var(--muted);
				}

				.schema-info:not(.permission) td:nth-child(3)::before {
					content: "Installed";
					color: var(--muted);
				}

				.schema-info.permission th, .schema-info.permission td {
					display: block;
					width: auto;
				}

				.schema-info.permission th {
					padding-bottom: 0;
					border-bottom: 0;
				}
			}
		</style>
	</head>
	
	<body>
		<main class="installer">
		<header class="installer-head">
			<p class="site-title"><?php echo htmlspecialchars((string)Flux::config('SiteTitle')) ?></p>
			<h1>Install &amp; Update</h1>
		</header>

		<div id="content">
			<?php if ($message=$session->getMessage()): ?>
				<p class="message" role="status"><?php echo htmlspecialchars((string)$message) ?></p>
			<?php endif ?>
			<?php if (!empty($errorMessage)): ?>
				<p class="error" role="alert"><?php echo htmlspecialchars((string)$errorMessage) ?></p>
			<?php endif ?>
