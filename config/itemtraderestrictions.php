<?php
// Hercules item_db.trade_flag / trade_group bits (enum ItemTradeRestrictions,
// src/map/itemdb.h).
return array(
	0x001 => 'Cannot be dropped',
	0x002 => 'Cannot be traded',
	0x004 => 'Wedded partner can override no-trade',
	0x008 => 'Cannot be sold to NPCs',
	0x010 => 'Cannot be stored in cart',
	0x020 => 'Cannot be stored in storage',
	0x040 => 'Cannot be stored in guild storage',
	0x080 => 'Cannot be attached to mail',
	0x100 => 'Cannot be auctioned',
)
?>
