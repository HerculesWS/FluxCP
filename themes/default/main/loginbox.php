<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if ($session->isLoggedIn()): ?>
<table cellspacing="0" cellpadding="0" width="100%" id="loginbox">
	<tr>
		<td width="18"><img src="<?php echo $this->themePath('img/loginbox_tl.gif') ?>" style="display: block" alt="" /></td>
		<td bgcolor="#e1eaf3"></td>
		<td width="18"><img src="<?php echo $this->themePath('img/loginbox_tr.gif') ?>" style="display: block" alt="" /></td>
	</tr>
	<tr>
		<td bgcolor="#e1eaf3"></td>
		<td bgcolor="#e1eaf3" valign="middle">
			<span style="display: inline-block; margin: 2px 2px 2px 0">
				<?php echo htmlspecialchars(Flux::message('LoggedInAsLabel')) ?> <strong><a href="<?php echo htmlspecialchars($this->url('account', 'view')) ?>" title="<?php echo htmlspecialchars(Flux::message('ViewAccountTitle')) ?>"><?php echo htmlspecialchars($session->account->userid) ?></a></strong>
				<?php echo htmlspecialchars(sprintf(Flux::message('LoggedInOnServerLabel'), $session->serverName)) ?>

			<?php if (count($athenaServerNames=$session->getAthenaServerNames()) > 1): ?>
				<?php echo htmlspecialchars(Flux::message('PreferredServerLabel')) ?>

			<select name="preferred_server" onchange="updatePreferredServer(this)"<?php if (count($athenaServerNames=$session->getAthenaServerNames()) === 1) echo ' disabled="disabled"'  ?>>
				<?php foreach ($athenaServerNames as $serverName): ?>
				<option value="<?php echo htmlspecialchars($serverName) ?>"<?php if ($server->serverName == $serverName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($serverName) ?></option>
				<?php endforeach ?>
			</select>.
			<?php endif ?>
			<form action="<?php echo htmlspecialchars($this->urlWithQs) ?>" method="post" name="preferred_server_form" style="display: none">
				<input type="hidden" name="preferred_server" value="" />
			</form>
			</span>
		</td>
		<td bgcolor="#e1eaf3"></td>
	</tr>
	<?php if (!empty($adminMenuItems) && Flux::config('AdminMenuNewStyle')): ?>
	<?php $mItems = array(); foreach ($adminMenuItems as $menuItem) $mItems[] = sprintf('<a href="%s">%s</a>', htmlspecialchars($menuItem['url']), htmlspecialchars(Flux::menuLabel($menuItem['name']))) ?>
	<tr>
		<td bgcolor="#e1eaf3"></td>
		<td bgcolor="#e1eaf3" valign="middle" class="loginbox-admin-menu">
			<strong><?php echo htmlspecialchars(Flux::message('AdminLabel')) ?></strong>: <?php echo implode(' • ', $mItems) ?>
		</td>
		<td bgcolor="#e1eaf3"></td>
	</tr>
	<?php endif ?>
	<tr>
		<td><img src="<?php echo $this->themePath('img/loginbox_bl.gif') ?>" style="display: block" alt="" /></td>
		<td bgcolor="#e1eaf3"></td>
		<td><img src="<?php echo $this->themePath('img/loginbox_br.gif') ?>" style="display: block" alt="" /></td>
	</tr>
</table>
<?php endif ?>