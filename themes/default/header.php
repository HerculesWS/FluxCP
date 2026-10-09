<?php if (!defined('FLUX_ROOT')) exit; ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php if (isset($metaRefresh)): ?>
		<meta http-equiv="refresh" content="<?php echo $metaRefresh['seconds'] ?>; URL=<?php echo htmlspecialchars((string)$metaRefresh['location']) ?>">
		<?php endif ?>
		<title><?php echo Flux::config('SiteTitle'); if (isset($title)) echo ": $title" ?></title>
		<link rel="stylesheet" href="<?php echo $this->themePath('css/flux.css') ?>">
		<link rel="stylesheet" href="<?php echo $this->themePath('css/flux/unitip.css') ?>">
		<?php if (Flux::config('EnableReCaptcha')): ?>
		<link rel="stylesheet" href="<?php echo $this->themePath('css/flux/recaptcha.css') ?>">
		<?php endif ?>
		<!--[if IE]>
		<link rel="stylesheet" href="<?php echo $this->themePath('css/flux/ie.css') ?>">
		<![endif]-->	
		<!--[if lt IE 9]>
		<script src="<?php echo $this->themePath('js/ie9.js') ?>"></script>
		<script src="<?php echo $this->themePath('js/flux.unitpngfix.js') ?>"></script>
		<![endif]-->
		<script src="<?php echo $this->themePath('js/jquery-1.8.3.min.js') ?>"></script>
		<script src="<?php echo $this->themePath('js/flux.datefields.js') ?>"></script>
		<script src="<?php echo $this->themePath('js/flux.unitip.js') ?>"></script>
		<script src="<?php echo $this->themePath('js/flux.tablecards.js') ?>"></script>
		<script src="<?php echo $this->themePath('js/flux.searchform.js') ?>"></script>
		<script src="<?php echo $this->themePath('js/flux.filterbar.js') ?>"></script>
		<script src="<?php echo $this->themePath('js/flux.datepicker.js') ?>"></script>
		<script>
			$(document).ready(function(){
				$('.menuitem a').hover(
					function(){
						$(this).fadeTo(200, 0.85);
						$(this).css('cursor', 'pointer');
					},
					function(){
						$(this).fadeTo(150, 1.00);
						$(this).css('cursor', 'normal');
					}
				);
				$('.money-input').keyup(function() {
					var creditValue = parseInt($(this).val() / <?php echo Flux::config('CreditExchangeRate') ?>, 10);
					if (isNaN(creditValue))
						$('.credit-input').val('?');
					else
						$('.credit-input').val(creditValue);
				}).keyup();
				$('.credit-input').keyup(function() {
					var moneyValue = parseFloat($(this).val() * <?php echo Flux::config('CreditExchangeRate') ?>);
					if (isNaN(moneyValue))
						$('.money-input').val('?');
					else
						$('.money-input').val(moneyValue.toFixed(2));
				}).keyup();
				
				// In: js/flux.datefields.js
				processDateFields();
			});
			
			function reload(){
				window.location.href = '<?php echo $this->url ?>';
			}
		</script>
		
		<script>
			function updatePreferredServer(sel){
				var preferred = sel.options[sel.selectedIndex].value;
				document.preferred_server_form.preferred_server.value = preferred;
				document.preferred_server_form.submit();
			}
			function updatePreferredTheme(sel){
				var preferred = sel.options[sel.selectedIndex].value;
				document.preferred_theme_form.preferred_theme.value = preferred;
				document.preferred_theme_form.submit();
			}
			// Preload spinner image.
			var spinner = new Image();
			spinner.src = '<?php echo $this->themePath('img/spinner.gif') ?>';
			
			function refreshSecurityCode(imgSelector){
				$(imgSelector).attr('src', spinner.src);
				
				// Load image, spinner will be active until loading is complete.
				var clean = <?php echo Flux::config('UseCleanUrls') ? 'true' : 'false' ?>;
				var image = new Image();
				image.src = "<?php echo $this->url('captcha') ?>"+(clean ? '?nocache=' : '&nocache=')+Math.random();
				
				$(imgSelector).attr('src', image.src);
			}
			function toggleSearchForm()
			{
				//$('.search-form').toggle();
				$('.search-form').slideToggle('fast');
			}
		</script>
		
	</head>
	<body>
		<header id="site-header">
			<a href="<?php echo $this->basePath ?>">
				<img src="<?php echo $this->themePath($session->account->group_level >= Flux::config('AdminMenuGroupLevel') ? 'img/logo_admin.gif' : 'img/logo.gif') ?>" id="logo" alt="<?php echo htmlspecialchars((string)Flux::config('ServerName')) ?>">
			</a>
		</header>
		<div id="wrapper">
			<aside id="sidebar-column">
				<input type="checkbox" id="nav-toggle" class="nav-toggle">
				<label for="nav-toggle" class="nav-toggle-label">Menu</label>
				<!-- Sidebar -->
				<?php include $this->themePath('main/sidebar.php', true) ?>
			</aside>
			<main id="main-column">
				<!-- Login box / User information -->
				<?php include $this->themePath('main/loginbox.php', true) ?>

				<!-- Content -->
				<div id="content">
					<?php if (Flux::config('DebugMode') && @gethostbyname(Flux::config('ServerAddress')) == '127.0.0.1'): ?>
						<p class="notice">Please change your <strong>ServerAddress</strong> directive in your application config to your server's real address (e.g., myserver.com).</p>
					<?php endif ?>

					<!-- Messages -->
					<?php if ($message=$session->getMessage()): ?>
						<p class="message"><?php echo htmlspecialchars((string)$message) ?></p>
					<?php endif ?>

					<!-- Sub menu -->
					<?php include $this->themePath('main/submenu.php', true) ?>

					<!-- Page menu -->
					<?php include $this->themePath('main/pagemenu.php', true) ?>

					<!-- Credit balance -->
					<?php if (in_array($params->get('module'), array('donate', 'purchase'))) include $this->themePath('main/balance.php', true) ?>
