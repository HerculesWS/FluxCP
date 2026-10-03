<?php
if (!defined('FLUX_ROOT')) exit;
$adminMenuItems = $this->getAdminMenuItems();
$menuItems = $this->getMenuItems();
?>

<?php if (!empty($adminMenuItems) && !Flux::config('AdminMenuNewStyle')): ?>
<nav id="admin_sidebar">
	<div class="sidebar-cap"><img src="<?php echo $this->themePath('img/sidebar_admin_complete_top.gif') ?>" alt=""></div>
	<div class="menuitem menu-heading"><strong><?php echo htmlspecialchars(Flux::message('AdminLabel')) ?></strong></div>
	<?php foreach ($adminMenuItems as $menuItem): ?>
	<div class="menuitem">
		<a href="<?php echo htmlspecialchars($this->url($menuItem['module'], $menuItem['action'])) ?>"<?php
			if ($menuItem['module'] == 'account' && $menuItem['action'] == 'logout')
				echo ' onclick="return confirm(\'Are you sure you want to logout?\')"' ?>>
			<span><?php echo htmlspecialchars(Flux::menuLabel($menuItem['name'])) ?></span>
		</a>
	</div>
	<?php endforeach ?>
	<div class="sidebar-cap"><img src="<?php echo $this->themePath('img/sidebar_admin_complete_bottom.gif') ?>" alt=""></div>
</nav>
<?php endif ?>

<?php if (!empty($menuItems)): ?>
<nav id="sidebar">
	<div class="sidebar-cap"><img src="<?php echo $this->themePath('img/sidebar_complete_top.gif') ?>" alt=""></div>
	<?php foreach ($menuItems as $menuCategory => $menus): ?>
	<?php if (!empty($menus)): ?>
	<div class="menuitem menu-heading"><strong><?php echo htmlspecialchars(Flux::menuLabel($menuCategory)) ?></strong></div>
	<?php foreach ($menus as $menuItem):  ?>
	<div class="menuitem">
		<a href="<?php echo htmlspecialchars((string)$menuItem['url']) ?>"<?php
			if ($menuItem['module'] == 'account' && $menuItem['action'] == 'logout')
				echo ' onclick="return confirm(\'Are you sure you want to logout?\')"' ?>>
			<span><?php echo htmlspecialchars(Flux::menuLabel($menuItem['name'])) ?></span>
		</a>
	</div>
	<?php endforeach ?>
	<?php endif ?>
	<?php endforeach ?>
	<div class="sidebar-cap"><img src="<?php echo $this->themePath('img/sidebar_complete_bottom.gif') ?>" alt=""></div>
</nav>
<?php endif ?>
