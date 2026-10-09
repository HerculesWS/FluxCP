<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Preferences</h2>
<?php if ($char): ?>
<?php if (!empty($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars((string)$errorMessage) ?></p>
<?php endif ?>
<h3>Viewing character preferences for “<?php echo ($charName=htmlspecialchars((string)$char->name))  ?>” on <?php echo htmlspecialchars((string)$server->serverName) ?></h3>
<form action="<?php echo htmlspecialchars((string)$this->urlWithQs) ?>" method="post" class="generic-form">
	<input type="hidden" name="charprefs" value="1" />
	<?php echo Flux_Security::csrfGenerate('CharacterPreferences', true) ?>

	<div class="pref-list">
		<div class="pref-item">
			<label for="hide_from_whos_online"><input type="checkbox" name="hide_from_whos_online" id="hide_from_whos_online"<?php if ($hideFromWhosOnline) echo ' checked="checked"' ?> /> Hide Character From "Who's Online"</label>
			<p class="pref-desc">This will hide <?php echo $charName ?> altogether from the "Who's Online" page.</p>
		</div>
		<div class="pref-item">
			<label for="hide_map_from_whos_online"><input type="checkbox" name="hide_map_from_whos_online" id="hide_map_from_whos_online"<?php if ($hideMapFromWhosOnline) echo ' checked="checked"' ?> /> Hide Current Map From "Who's Online"</label>
			<p class="pref-desc">This will hide <?php echo $charName ?>'s current location from the "Who's Online" page.</p>
		</div>
		<?php if ($auth->allowedToHideFromZenyRank): ?>
		<div class="pref-item">
			<label for="hide_from_zeny_ranking"><input type="checkbox" name="hide_from_zeny_ranking" id="hide_from_zeny_ranking"<?php if ($hideFromZenyRanking) echo ' checked="checked"' ?> /> Hide Character From "Zeny Ranking"</label>
			<p class="pref-desc">This will hide <?php echo $charName ?> from the "Zeny Ranking" page.</p>
		</div>
		<?php endif ?>
		<div class="pref-actions">
			<input type="submit" value="Modify Preferences" />
		</div>
	</div>
</form>
<?php else: ?>
<p>No such character found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>