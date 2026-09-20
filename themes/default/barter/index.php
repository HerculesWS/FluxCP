<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Barter Shops</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="npc">NPC Name:</label>
		<input type="text" name="npc" id="npc" value="<?php echo htmlspecialchars((string)$params->get('npc')) ?>" />
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>
<?php if ($barters): ?>
<table class="horizontal-table">
	<tr>
		<th>NPC</th>
		<th>Item</th>
		<th>Amount</th>
		<th>Price</th>
	</tr>
	<?php foreach ($barters as $barter): ?>
	<tr>
		<td><?php echo htmlspecialchars($barter->name) ?></td>
		<td>
			<?php if ($barter->item_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($barter->itemId, $barter->item_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($barter->item_name) ?>
				<?php endif ?>
			<?php else: ?>
				<?php echo htmlspecialchars($barter->itemId) ?>
			<?php endif ?>
		</td>
		<td><?php echo number_format((int)$barter->amount) ?></td>
		<td>
			<?php if ($barter->barter_type == 'simple'): ?>
				<?php if ($barter->price_item_name): ?>
					<?php if ($auth->actionAllowed('item', 'view')): ?>
						<?php echo number_format((int)$barter->priceAmount) ?>x <?php echo $this->linkToItem($barter->priceId, $barter->price_item_name) ?>
					<?php else: ?>
						<?php echo number_format((int)$barter->priceAmount) ?>x <?php echo htmlspecialchars($barter->price_item_name) ?>
					<?php endif ?>
				<?php else: ?>
					<?php echo number_format((int)$barter->priceAmount) ?>x Item #<?php echo htmlspecialchars($barter->priceId) ?>
				<?php endif ?>
			<?php else: ?>
				<?php if ($barter->zeny): ?>
					<?php echo number_format((int)$barter->zeny) ?> Zeny
					<?php if ($barter->currencyId1): ?> + <?php endif ?>
				<?php endif ?>
				<?php if ($barter->currencyId1): ?>
					<?php echo number_format((int)$barter->currencyAmount1) ?>x
					<?php if ($barter->currency_item_name): ?>
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<?php echo $this->linkToItem($barter->currencyId1, $barter->currency_item_name) ?>
						<?php else: ?>
							<?php echo htmlspecialchars($barter->currency_item_name) ?>
						<?php endif ?>
					<?php else: ?>
						Item #<?php echo htmlspecialchars($barter->currencyId1) ?>
					<?php endif ?>
					<?php if ($barter->currencyRefine1): ?>
						(+<?php echo (int)$barter->currencyRefine1 ?>)
					<?php endif ?>
				<?php endif ?>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php else: ?>
<p>No barter shop entries found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
