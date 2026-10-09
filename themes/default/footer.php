<?php if (!defined('FLUX_ROOT')) exit; ?>
				</div>
			</main>
		</div>
		<footer id="site-footer">
			<?php if (Flux::config('ShowCopyright')): ?>
			<div id="copyright">
				<p>
					<strong>Powered by <a href="https://github.com/HerculesWS/FluxCP">FluxCP</a> and <a href="https://github.com/HerculesWS/Hercules">Hercules</a>.</strong>
					&mdash;  Version <?php echo htmlspecialchars(Flux::VERSION) ?> &#64;<?php echo Flux::REPOSVERSION ? Flux::REPOSVERSION : '' ?>
				</p>
			</div>
			<?php endif ?>
			<?php if (Flux::config('ShowRenderDetails')): ?>
			<div id="info">
				<p>
					Page generated in <strong><?php echo round(microtime(true) - __START__, 5) ?></strong> second(s).
					Number of queries executed: <strong><?php echo (int)Flux::$numberOfQueries ?></strong>.
					<?php if (Flux::config('GzipCompressOutput')): ?>Gzip Compression: <strong>Enabled</strong>.<?php endif ?>
				</p>
			</div>
			<?php endif ?>

			<?php if (count(Flux::$appConfig->get('ThemeName', false)) > 1): ?>
			<div id="theme-select">
				<span>Theme:
				<select name="preferred_theme" onchange="updatePreferredTheme(this)">
					<?php foreach (Flux::$appConfig->get('ThemeName', false) as $themeName): ?>
					<option value="<?php echo htmlspecialchars((string)$themeName) ?>"<?php if ($session->theme == $themeName) echo ' selected="selected"' ?>><?php echo htmlspecialchars((string)$themeName) ?></option>
					<?php endforeach ?>
				</select>
				</span>

				<form action="<?php echo htmlspecialchars((string)$this->urlWithQs) ?>" method="post" name="preferred_theme_form" style="display: none">
				<input type="hidden" name="preferred_theme" value="">
				</form>
			</div>
			<?php endif ?>
		</footer>
	</body>
</html>
