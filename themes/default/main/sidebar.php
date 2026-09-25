<?php
if (!defined('FLUX_ROOT')) exit;
$adminMenuItems = $this->getAdminMenuItems();
$menuItems = $this->getMenuItems();
?>

<?php if (!empty($adminMenuItems) && !Flux::config('AdminMenuNewStyle')): ?>
<table id="admin_sidebar">
	<tr>
		<td><img src="<?php echo $this->themePath('img/sidebar_admin_complete_top.gif') ?>" alt="" /></td>
	</tr>
	<tr>
		<th class="menuitem"><strong><?php echo htmlspecialchars(Flux::message('AdminLabel')) ?></strong></td>
	</tr>
	<?php foreach ($adminMenuItems as $menuItem): ?>
	<tr>
		<td class="menuitem">
			<a href="<?php echo htmlspecialchars($this->url($menuItem['module'], $menuItem['action'])) ?>"<?php
				if ($menuItem['module'] == 'account' && $menuItem['action'] == 'logout')
					echo ' onclick="return confirm(\'Are you sure you want to logout?\')"' ?>>
				<span><?php echo htmlspecialchars(Flux::menuLabel($menuItem['name'])) ?></span>
			</a>
		</td>
	</tr>
	<?php endforeach ?>
	<tr>
		<td><img src="<?php echo $this->themePath('img/sidebar_admin_complete_bottom.gif') ?>" alt="" /></td>
	</tr>
</table>
<?php endif ?>

<?php if (!empty($menuItems)): ?>
<table id="sidebar">
	<tr>
		<td><img src="<?php echo $this->themePath('img/sidebar_complete_top.gif') ?>" alt="" /></td>
	</tr>
	<?php foreach ($menuItems as $menuCategory => $menus): ?>
	<?php if (!empty($menus)): ?>
	<tr>
		<th class="menuitem"><strong><?php echo htmlspecialchars(Flux::menuLabel($menuCategory)) ?></strong></th>
	</tr>
	<?php foreach ($menus as $menuItem):  ?>
	<tr>
		<td class="menuitem">
			<a href="<?php echo htmlspecialchars($menuItem['url']) ?>"<?php
				if ($menuItem['module'] == 'account' && $menuItem['action'] == 'logout')
					echo ' onclick="return confirm(\'Are you sure you want to logout?\')"' ?>>
				<span><?php echo htmlspecialchars(Flux::menuLabel($menuItem['name'])) ?></span>
			</a>
		</td>
	</tr>
	<?php endforeach ?>
	<?php endif ?>
	<?php endforeach ?>
	<tr>
		<td><img src="<?php echo $this->themePath('img/sidebar_complete_bottom.gif') ?>" alt="" /></td>
	</tr>
</table>
<?php endif ?>