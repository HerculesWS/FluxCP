<?php
if (!defined('FLUX_ROOT')) exit;
$adminMenuItems = $this->getAdminMenuItems();
$menuItems = $this->getMenuItems();
?>

<!-- NAVIGATION START -->
<?php if (!empty($menuItems) || !empty($adminMenuItems)): ?>
	<nav role="navigation" class="site-navigation main-navigation">
		<div class="home-page-link"><a href="<?php echo $this->url('main') ?>" class="icon-home" title="<?php echo htmlspecialchars(Flux::message('HomeLabel')) ?>"><span><?php echo htmlspecialchars(Flux::message('HomeLabel')) ?></span></a></div>
		<?php if ($session->isLoggedIn()): ?>
		<div class="server-select">
			<?php if (count($athenaServerNames=$session->getAthenaServerNames()) > 1): ?>
			<span><?php echo htmlspecialchars(Flux::message('AccountServerLabel')) ?>:
			<select name="preferred_server" onchange="updatePreferredServer(this)"<?php if (count($athenaServerNames=$session->getAthenaServerNames()) === 1) echo ' disabled="disabled"'  ?>>
				<?php foreach ($athenaServerNames as $serverName): ?>
				<option value="<?php echo htmlspecialchars($serverName) ?>"<?php if ($server->serverName == $serverName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($serverName) ?></option>
				<?php endforeach ?>
			</select>
			</span>
			<?php endif ?>
			<form action="<?php echo $this->urlWithQs ?>" method="post" name="preferred_server_form" style="display: none">
				<input type="hidden" name="preferred_server" value="" />
			</form>
		</div>
		<?php endif ?>
		<div>
			<ul id="primary-nav" class="menu">
			<?php if (!empty($menuItems)): ?>
				<?php foreach ($menuItems as $menuCategory => $menus): ?>
				<?php if (!empty($menus)): ?>
						<li class="menu-item"><a href="#"><?php echo htmlspecialchars(Flux::menuLabel($menuCategory)) ?></a>
							<ul class="sub-menu">
							<?php foreach ($menus as $menuItem): ?>
								<li class="menu-item">
										<a href="<?php echo $menuItem['url'] ?>"<?php
								if ($menuItem['module'] == 'account' && $menuItem['action'] == 'logout')
										echo ' onclick="return confirm(\'Are you sure you want to logout?\')"' ?>>
									<?php echo htmlspecialchars(Flux::menuLabel($menuItem['name'])) ?></a>
								</li>
							<?php endforeach ?>
							</ul>
						</li>
				<?php endif ?>
				<?php endforeach ?>
			<?php endif ?>
			<?php if (!empty($adminMenuItems)): ?>
				<li class="menu-item"><a href="#"><?php echo htmlspecialchars(Flux::message('AdminLabel')) ?></a>
				<ul class="sub-menu">
							<?php foreach ($adminMenuItems as $menuItem): ?>
								<li class="menu-item">
										<a href="<?php echo $menuItem['url'] ?>"<?php
									if ($menuItem['module'] == 'account' && $menuItem['action'] == 'logout')
											echo ' onclick="return confirm(\'Are you sure you want to logout?\')"' ?>>
									<?php echo htmlspecialchars(Flux::menuLabel($menuItem['name'])) ?></a>
								</li>
							<?php endforeach ?>
				</ul>
				</li>
			<?php endif ?>
			</ul>
		</div>
	</nav>
<!-- NAVIGATION END -->
<?php endif ?>
