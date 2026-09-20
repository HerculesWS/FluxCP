<?php if (!defined('FLUX_ROOT')) exit; ?>
				</article>
			</div><!-- #content .site-content -->
		</div><!-- #primary .content-area -->
	</div><!-- #main .site-main -->


	<footer class="site-footer">


		<div class="widget-area footer-widget-area" role="complementary">
			<?php include('main/footer-area.php') ?>
		</div>

		<div id="bottom-bar">
			<?php if (Flux::config('ShowRenderDetails')): ?>
			<div class="footer-text">
				Page generated in <strong><?php echo round(microtime(true) - __START__, 5) ?></strong> second(s).
				Number of queries executed: <strong><?php echo (int)Flux::$numberOfQueries ?></strong>.
				<?php if (Flux::config('GzipCompressOutput')): ?>Gzip Compression: <strong>Enabled</strong>.<?php endif ?>
			</div>
			<?php endif ?>

			<div class="site-info">
			<?php if (Flux::config('ShowCopyright')): ?>
			Powered by <a href="http://github.com/HerculesWS/FluxCP/">FluxCP</a> and <a href="http://github.com/HerculesWS/Hercules/">Hercules</a>. 
			<?php endif ?>
			<?php if (count(Flux::$appConfig->get('ThemeName', false)) > 1): ?>
			<span>Theme:
			<select name="preferred_theme" onchange="updatePreferredTheme(this)">
				<?php foreach (Flux::$appConfig->get('ThemeName', false) as $themeName): ?>
				<option value="<?php echo htmlspecialchars($themeName) ?>"<?php if ($session->theme == $themeName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($themeName) ?></option>
				<?php endforeach ?>
			</select>
			</span>
			<?php endif ?>
			<form action="<?php echo htmlspecialchars($this->urlWithQs) ?>" method="post" name="preferred_theme_form" style="display: none">
				<input type="hidden" name="preferred_theme" value="" />
			</form>
			</div>


		</div>
	</footer>
</div>
</body>
</html>
