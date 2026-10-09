<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('ServerStatusHeading')) ?></h2>
<p><?php echo htmlspecialchars(Flux::message('ServerStatusInfo')) ?></p>
<div class="export-bar">
	<span class="export-label">Export</span>
	<a href="<?php echo htmlspecialchars($this->url('server', 'status-xml')) ?>" class="export-link">XML</a>
	<a href="<?php echo htmlspecialchars($this->url('server', 'status-json')) ?>" class="export-link">JSON</a>
</div>
<?php foreach ($serverStatus as $privServerName => $gameServers): ?>
<h3>Server Status for <?php echo htmlspecialchars((string)$privServerName) ?></h3>
<div class="server-status">
	<?php foreach ($gameServers as $serverName => $gameServer): ?>
	<div class="server-status-card">
		<?php if (count($gameServers) > 1): ?>
		<h4 class="server"><?php echo htmlspecialchars((string)$serverName) ?></h4>
		<?php endif ?>
		<div class="status-grid">
			<div class="status-tile">
				<span class="status-label"><?php echo htmlspecialchars(Flux::message('ServerStatusLoginLabel')) ?></span>
				<span class="status-value"><?php echo $this->serverUpDown($gameServer['loginServerUp']) ?></span>
			</div>
			<div class="status-tile">
				<span class="status-label"><?php echo htmlspecialchars(Flux::message('ServerStatusCharLabel')) ?></span>
				<span class="status-value"><?php echo $this->serverUpDown($gameServer['charServerUp']) ?></span>
			</div>
			<div class="status-tile">
				<span class="status-label"><?php echo htmlspecialchars(Flux::message('ServerStatusMapLabel')) ?></span>
				<span class="status-value"><?php echo $this->serverUpDown($gameServer['mapServerUp']) ?></span>
			</div>
			<div class="status-tile">
				<span class="status-label"><?php echo htmlspecialchars(Flux::message('ServerStatusOnlineLabel')) ?></span>
				<span class="status-value"><?php echo $gameServer['playersOnline'] ?></span>
			</div>
			<div class="status-tile">
				<span class="status-label"><?php echo htmlspecialchars(Flux::message('ServerStatusATMerchantsLabel')) ?></span>
				<span class="status-value"><?php echo $gameServer['autotradeMerchants'] ?></span>
			</div>
			<div class="status-tile">
				<span class="status-label"><?php echo htmlspecialchars(Flux::message('ServerStatusPopulationLabel')) ?></span>
				<span class="status-value"><?php echo $gameServer['population'] ?></span>
			</div>
			<?php $peak = isset($serverPeak[$privServerName][$serverName]) ? $serverPeak[$privServerName][$serverName] : null ?>
			<?php if ($peak): ?>
			<div class="status-tile">
				<span class="status-label">Peak Users</span>
				<span class="status-value" title="Most players online at once: <?php echo (int)$peak['peak'] ?>, reached <?php echo htmlspecialchars($this->formatDateTime(date('Y-m-d H:i:s', (int)$peak['time']))) ?>"><?php echo number_format((int)$peak['peak']) ?></span>
			</div>
			<?php endif ?>
		</div>
		<?php $woe = isset($woeStatus[$privServerName][$serverName]) ? $woeStatus[$privServerName][$serverName] : null ?>
		<?php if ($woe && $woe['configured']): ?>
		<div class="status-tile status-tile-wide">
			<span class="status-label">War of Emperium</span>
			<span class="status-value">
				<?php if ($woe['active']): ?>
				<span class="woe-pill woe-live">In progress</span>
				<?php else: ?>
				<span class="woe-pill woe-idle">Not active</span>
				<?php endif ?>
			</span>
			<?php if ($woe['when']): ?>
			<span class="status-note">
				<?php if ($woe['active']): ?>Ends <?php echo htmlspecialchars($woe['until']) ?><?php else: ?>Next: <?php echo htmlspecialchars($woe['when']) ?><?php endif ?>
				<?php if (!$woe['active'] && $woe['in']): ?>(in <?php echo htmlspecialchars($woe['in']) ?>)<?php endif ?>
			</span>
			<?php if ($woe['castles']): ?>
			<span class="woe-castles woe-castles-center">
				<?php foreach ($woe['castles'] as $castleName): ?>
				<span class="woe-castle"><?php echo htmlspecialchars((string)$castleName) ?></span>
				<?php endforeach ?>
			</span>
			<?php endif ?>
			<?php endif ?>
		</div>
		<?php endif ?>
	</div>
	<?php endforeach ?>
</div>
<?php endforeach ?>