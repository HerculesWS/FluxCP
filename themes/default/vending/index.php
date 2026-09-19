<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Vending Shops</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="char_name">Vendor Name:</label>
		<input type="text" name="char_name" id="char_name" value="<?php echo htmlspecialchars((string)$params->get('char_name')) ?>" />
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>
<?php if ($vendors): ?>
<?php foreach ($vendors as $vendor): ?>
<h3>
	<?php if ($vendor['char_name']): ?>
		<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
			<?php echo $this->linkToCharacter($vendor['char_id'], $vendor['char_name']) ?>
		<?php else: ?>
			<?php echo htmlspecialchars($vendor['char_name']) ?>
		<?php endif ?>
	<?php else: ?>
		<?php echo htmlspecialchars($vendor['char_id']) ?>
	<?php endif ?>
	&mdash; <?php echo htmlspecialchars($vendor['title']) ?>
	<?php if ($vendor['map']): ?>
		<span class="not-applicable">(<?php echo htmlspecialchars($vendor['map']) ?> <?php echo (int)$vendor['x'] ?>, <?php echo (int)$vendor['y'] ?>)</span>
	<?php endif ?>
</h3>
<?php if ($vendor['items']): ?>
<table class="horizontal-table">
	<tr>
		<th>Item</th>
		<th>Refine</th>
		<th>Amount</th>
		<th>Price</th>
	</tr>
	<?php foreach ($vendor['items'] as $item): ?>
	<tr>
		<td>
			<?php if ($item->item_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($item->nameid, $item->item_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($item->item_name) ?>
				<?php endif ?>
			<?php else: ?>
				<?php echo htmlspecialchars($item->nameid) ?>
			<?php endif ?>
		</td>
		<td><?php echo $item->refine ? '+'.(int)$item->refine : '' ?></td>
		<td><?php echo number_format((int)$item->amount) ?></td>
		<td><?php echo number_format((int)$item->price) ?> Zeny</td>
	</tr>
	<?php endforeach ?>
</table>
<?php else: ?>
<p><span class="not-applicable">This shop has no items for sale.</span></p>
<?php endif ?>
<?php endforeach ?>
<?php else: ?>
<p>No vending shops found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
