<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'Checkout Area';

if ($server->cart->isEmpty()) {
	$session->setMessageData('Your cart is currently empty.');
	$this->redirect($this->url('purchase'));
}
elseif (!$server->cart->hasFunds()) {
	$session->setMessageData('You do not have sufficient funds to make this purchase!');
	$this->redirect($this->url('purchase'));
}

$items = $server->cart->getCartItems();

if (count($_POST) && $params->get('process')) {

	if ( !Flux_Security::csrfValidate('PurchaseCheckOut', $_POST, $error) ) {
		$session->setMessageData($error);
		$this->redirect($this->url('purchase','checkout'));
	}

	$redeemTable = Flux::config('FluxTables.RedemptionTable');
	$accountID   = $session->account->account_id;
	
	// Take the credits first and only if they are there, items are issued after that.
	if (!$session->loginServer->spendCredits($accountID, $server->cart->getTotal())) {
		$session->setMessageData('You do not have sufficient funds to make this purchase!');
		$this->redirect($this->url('purchase'));
	}
	
	$sql  = "INSERT INTO {$server->charMapDatabase}.$redeemTable ";
	$sql .= "(nameid, quantity, cost, account_id, char_id, redeemed, redemption_date, purchase_date, credits_before, credits_after) ";
	$sql .= "VALUES (?, ?, ?, ?, NULL, 0, NULL, NOW(), ?, ?)";
	$sth  = $server->connection->getStatement($sql);
	
	$balance   = $session->account->balance;
	$purchased = 0;
	$refund    = 0;
	
	foreach ($items as $item) {
		$creditsAfter = $balance - $item->shop_item_cost;
		
		$res = $sth->execute(array(
			$item->shop_item_nameid,
			$item->shop_item_qty,
			$item->shop_item_cost,
			$accountID,
			$balance,
			$creditsAfter
		));
		
		if ($res) {
			$purchased++;
			$balance -= $item->shop_item_cost;
		}
		else {
			$refund += $item->shop_item_cost;
		}
	}
	
	// Give back the credits for anything that could not be issued.
	if ($refund) {
		$session->loginServer->depositCredits($accountID, $refund);
	}
	
	$server->cart->clear();
	
	if (!$purchased) {
		$session->setMessageData('Failed to purchase all of the items in your cart!');
	}
	elseif ($refund) {
		$session->setMessageData('Items have been purchased, however, some failed (your credits are still there.)');
	}
	else {
		$session->setMessageData('Items have been purchased.  You may redeem them from the Redemption NPC.');
	}
	
	$this->redirect();
}
?>