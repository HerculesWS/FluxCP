<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if ($session->isLoggedIn()): ?>
<div id="loginbox">
	<div class="loginbox-user">
		<span>
			<?php echo htmlspecialchars(Flux::message('LoggedInAsLabel')) ?> <strong><a href="<?php echo htmlspecialchars($this->url('account', 'view')) ?>" title="<?php echo htmlspecialchars(Flux::message('ViewAccountTitle')) ?>"><?php echo htmlspecialchars((string)$session->account->userid) ?></a></strong>
			<?php echo htmlspecialchars(sprintf(Flux::message('LoggedInOnServerLabel'), $session->serverName)) ?>

		<?php if (count($athenaServerNames=$session->getAthenaServerNames()) > 1): ?>
			<?php echo htmlspecialchars(Flux::message('PreferredServerLabel')) ?>

		<select name="preferred_server" onchange="updatePreferredServer(this)"<?php if (count($athenaServerNames=$session->getAthenaServerNames()) === 1) echo ' disabled="disabled"'  ?>>
			<?php foreach ($athenaServerNames as $serverName): ?>
			<option value="<?php echo htmlspecialchars((string)$serverName) ?>"<?php if ($server->serverName == $serverName) echo ' selected="selected"' ?>><?php echo htmlspecialchars((string)$serverName) ?></option>
			<?php endforeach ?>
		</select>.
		<?php endif ?>
		<form action="<?php echo htmlspecialchars((string)$this->urlWithQs) ?>" method="post" name="preferred_server_form" style="display: none">
			<input type="hidden" name="preferred_server" value="">
		</form>
		</span>
	</div>
	<?php if (!empty($adminMenuItems) && Flux::config('AdminMenuNewStyle')): ?>
	<?php $mItems = array(); foreach ($adminMenuItems as $menuItem) $mItems[] = sprintf('<a href="%s">%s</a>', htmlspecialchars((string)$menuItem['url']), htmlspecialchars(Flux::menuLabel($menuItem['name']))) ?>
	<div class="loginbox-admin-menu">
		<strong><?php echo htmlspecialchars(Flux::message('AdminLabel')) ?></strong>: <?php echo implode(' • ', $mItems) ?>
	</div>
	<?php endif ?>
</div>
<?php endif ?>
