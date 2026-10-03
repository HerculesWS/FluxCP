<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('WoeHeading')) ?></h2>
<?php if ($woeTimes): ?>
<p><?php echo htmlspecialchars(sprintf(Flux::message('WoeInfo'), $session->loginAthenaGroup->serverName)) ?></p>
<div class="form-callout woe-note">
	<p><?php echo htmlspecialchars(Flux::message('WoeServerTimeInfo')) ?> <strong class="important"><?php echo $server->getServerTime('Y-m-d H:i:s (l)') ?></strong>.</p>
</div>
<?php $multiServer = count($woeTimes) > 1 ?>
<div class="woe-servers<?php if (!$multiServer) echo ' woe-single' ?>">
	<?php foreach ($woeTimes as $serverName => $times): ?>
	<div class="woe-server">
		<?php if ($multiServer): ?>
		<h4 class="woe-server-name"><?php echo htmlspecialchars((string)$serverName) ?></h4>
		<?php endif ?>
		<p class="woe-caption"><?php echo htmlspecialchars(Flux::message('WoeTimesLabel')) ?></p>
		<ul class="woe-slots">
			<?php foreach ($times as $time): ?>
			<li class="woe-slot">
				<span class="woe-point">
					<strong><?php echo htmlspecialchars((string)$time['startingDay']) ?></strong>
					<span class="woe-clock"><?php echo htmlspecialchars((string)$time['startingHour']) ?></span>
				</span>
				<span class="woe-arrow">&rarr;</span>
				<span class="woe-point">
					<strong><?php echo htmlspecialchars((string)$time['endingDay']) ?></strong>
					<span class="woe-clock"><?php echo htmlspecialchars((string)$time['endingHour']) ?></span>
				</span>
				<?php if (!empty($time['castles'])): ?>
				<span class="woe-castles">
					<?php foreach ($time['castles'] as $castleName): ?>
					<span class="woe-castle"><?php echo htmlspecialchars((string)$castleName) ?></span>
					<?php endforeach ?>
				</span>
				<?php endif ?>
			</li>
			<?php endforeach ?>
		</ul>
	</div>
	<?php endforeach ?>
</div>
<?php else: ?>
<p><?php echo htmlspecialchars(Flux::message('WoeNotScheduledInfo')) ?></p>
<?php endif ?>