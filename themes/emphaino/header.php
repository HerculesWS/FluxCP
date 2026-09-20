<?php if (!defined('FLUX_ROOT')) exit; ?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php if (isset($metaRefresh)): ?>
		<meta http-equiv="refresh" content="<?php echo $metaRefresh['seconds'] ?>; URL=<?php echo $metaRefresh['location'] ?>" />
		<?php endif ?>
		<title><?php if (isset($title)) echo "$title &raquo; "; echo Flux::config('SiteTitle'); ?></title>
		<!--[if lt IE 9]>
		<script src="<?php echo $this->themePath('js/html5.js') ?>" type="text/javascript"></script>
		<![endif]-->
		<link rel='stylesheet' id='webfonts-css' href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,400italic|Bree+Serif' type='text/css' media='all' />
		<link rel='stylesheet' id='style-css' href='<?php echo $this->themePath('css/style.css') ?>' type='text/css' media='all' />
		<link rel='stylesheet' id='fontello-css'  href='<?php echo $this->themePath('lib/fontello/css/fontello.css?ver=1.2.2') ?>' type='text/css' media='all' />
		<?php if (Flux::config('EnableReCaptcha')): ?>
		<link rel='stylesheet' id='recaptcha-css' href='<?php echo $this->themePath('css/flux/recaptcha.css') ?>' type='text/css' media='screen' />
		<?php endif ?>
		<!--[if lt IE 9]>
		<link rel='stylesheet' id='ie-style-css' href='<?php echo $this->themePath('css/ie.css') ?>' type='text/css' media='screen, projection' />
		<![endif]-->
		<script type='text/javascript' src='<?php echo $this->themePath('js/jquery.js?ver=1.10.2') ?>'></script>
		<script type='text/javascript' src='<?php echo $this->themePath('js/flux.datefields.js') ?>'></script>
		<script type='text/javascript'>
			$(document).ready(function(){
				var inputs = 'input[type=text],input[type=password],input[type=file]';
				$(inputs).focus(function(){
					$(this).css({
						'background-color': '#f9f5e7',
						'border-color': '#dcd7c7',
						'color': '#726c58'
					});
				});
				$(inputs).blur(function(){
					$(this).css({
						'backgroundColor': '#ffffff',
						'borderColor': '#dddddd',
						'color': '#444444'
					}, 500);
				});
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
		
		<script type='text/javascript'>
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
	<body class="page">
	<div id="page" class="hfeed site">
		<header id="masthead" class="site-header" role="banner">


			<div class="header-main">
				<div class="site-branding alignleft">
					<h1 class="site-title"><?php echo Flux::config('SiteTitle'); ?></h1>
				</div>
				<div id="loginbox" class="loginbox alignright">
					<?php include $this->themePath('main/loginbox.php', true) ?>
				</div>
				<div class="clearfix"></div>
			</div>

			<div id="top-bar">
				<?php include $this->themePath('main/navigation.php', true) ?>
			</div>
			
			<div class="submenu-area  ">
				<!-- Sub menu -->
				<?php include $this->themePath('main/submenu.php', true) ?>
							
				<!-- Page menu -->
				<?php include $this->themePath('main/pagemenu.php', true) ?>
							
				<!-- Credit balance -->
				<?php if (in_array($params->get('module'), array('donate', 'purchase'))) include $this->themePath('main/balance.php', true) ?>
			</div>
			
		</header>

		<div id="main" class="site-main">
			<div id="primary" class="content-area">
				<div id="content" class="entry-content" role="main">

					<?php if (Flux::config('DebugMode') && @gethostbyname(Flux::config('ServerAddress')) == '127.0.0.1'): ?>
					<p class="notice">Please change your <strong>ServerAddress</strong> directive in your application config to your server's real address (e.g., myserver.com).</p>
					<?php endif ?>
								
					<!-- Messages -->
					<?php if ($message=$session->getMessage()): ?>
					<p class="message"><?php echo htmlspecialchars($message) ?></p>
					<?php endif ?>
								


					<article class="page hentry">