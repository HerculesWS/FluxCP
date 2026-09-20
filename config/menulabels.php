<?php
/**
 * Maps the plain-text menu/category/sub-menu names used in MenuItems and
 * SubMenuItems (in application.php) and in modules/*\/pagemenu/*.php to a
 * language key in lang/*.php.
 *
 * This is looked up by Flux::menuLabel(), which falls back to displaying
 * the plain text as-is when it has no entry here -- so a custom or addon
 * menu item never needs a translation to show up correctly, it just won't
 * be translated until an entry is added here and to the language files.
 */
return array(
	// MenuItems categories
	'Account'                     => 'AccountLabel',
	'Donations'                   => 'DonationsLabel',
	'Information'                 => 'InformationLabel',
	'Database'                    => 'DatabaseLabel',
	'Misc. Stuff'                 => 'MiscStuffLabel',

	// MenuItems: Account
	'Register'                    => 'AccountCreateHeading',
	'Login'                       => 'LoginLabel',
	'My Account'                  => 'MyAccountLabel',
	'History'                     => 'HistoryLabel',
	'Logout'                      => 'LogoutLabel',

	// MenuItems: Donations
	'Donate'                      => 'DonateLabel',
	'Purchase'                    => 'PurchaseLabel',

	// MenuItems: Information
	'Server Info'                 => 'ServerInfoLabel',
	'Server Status'               => 'ServerStatusLabel',
	'WoE Hours'                   => 'WoeHoursLabel',
	'Castles'                     => 'CastlesLabel',
	"Who's Online"                => 'WhosOnlineLabel',
	'Map Statistics'              => 'MapStaticsLabel',
	'Ranking Info'                => 'RankingInfoLabel',

	// MenuItems: Database
	'Item Database'                => 'ItemDatabaseLabel',
	'Mob Database'                 => 'MobDatabaseLabel',
	'Vending Shops'                => 'VendingInfoLabel',

	// MenuItems: Misc. Stuff (also used as admin menu item names)
	'Hercules Logs'                => 'HerculesLogsLabel',
	'CP Logs'                      => 'CpLogsLabel',
	'IP Ban List'                  => 'IpbanListLabel',
	'Accounts'                     => 'AccountsLabel',
	'Characters'                   => 'CharactersLabel',
	'Guilds'                       => 'GuildsLabel',
	'Send Mail'                    => 'SendMailLabel',
	'Re-Install'                   => 'ReInstallLabel',

	// SubMenuItems: history
	'Game Logins'                  => 'HistoryGameLoginHeading',
	'CP Logins'                    => 'CpLoginsLabel',
	'E-Mail Changes'               => 'HistoryEmailHeading',
	'Password Changes'             => 'HistoryPassChangeHeading',
	'Password Resets'              => 'HistoryPassResetHeading',

	// SubMenuItems: account
	'List Accounts'                => 'AccountIndexTitle',
	'View Account'                 => 'AccountViewTitle',
	'Change Password'              => 'PasswordChangeTitle',
	'Change E-mail'                => 'EmailChangeTitle',
	'Change Gender'                => 'GenderChangeTitle',
	'Transfer Credits'             => 'TransferTitleShort',
	'Credit Transfer History'      => 'XferLogTitle',
	'Go to Shopping Cart'          => 'CartLabel',
	'Reset Password'               => 'ResetPassTitle',
	'Resend E-mail Confirmation'   => 'ResendConfirmLabel',

	// SubMenuItems: guild
	'List Guilds'                  => 'ListGuildsLabel',
	'Export Guild Emblems'         => 'ExportGuildEmblemLabel',

	// SubMenuItems: server
	'View Status'                  => 'ViewStatusLabel',
	'View Status as XML'           => 'ViewXmlStatusLabel',

	// SubMenuItems: logdata
	'Commands'                     => 'CommandLogHeading',
	'Branches'                     => 'BranchesLabel',
	'Chat Messages'                => 'ChatMessagesLabel',
	'Logins'                       => 'LoginsLabel',
	'MVP'                          => 'MvpLabel',
	'NPC'                          => 'NpcLabel',
	'Item Picks'                   => 'PickLogHeading',
	'Zeny'                         => 'ZenyLabel',

	// SubMenuItems: cplog
	'PayPal Transactions'          => 'PaypalTransactionsTitle',
	'Account Bans'                 => 'AccountBansLabel',
	'IP Bans'                      => 'IpBansLabel',

	// SubMenuItems: purchase
	'Shop'                         => 'ShopLabel',
	'Go to Cart'                   => 'GoToCartLabel',
	'Checkout'                     => 'CheckoutLabel',
	'Empty Cart'                   => 'EmptyCartLabel',
	'Pending Redemption'           => 'PendingRedemptionLabel',

	// SubMenuItems: donate
	'Make a Donation'              => 'MakeDonationLabel',
	'Donation History'             => 'DonationHistoryLabel',
	'Trusted PayPal E-mails'       => 'TrustedPayPalMailsLabel',

	// SubMenuItems: ipban
	'Add IP Ban'                   => 'IpbanAddHeading',

	// SubMenuItems: ranking
	'Character Ranking'            => 'CharacterRankingLabel',
	'Guild Ranking'                => 'GuildRankingLabel',
	'Zeny Ranking'                 => 'ZenyRankingLabel',
	'Death Ranking'                => 'DeathRankingLabel',
	'Alchemist Ranking'            => 'AlchemistRankingLabel',
	'Blacksmith Ranking'           => 'BlacksmithRankingLabel',

	// SubMenuItems: item
	'List Items'                   => 'ListItemsLabel',
	'Add Item'                     => 'AddItemLabel',

	// modules/character/pagemenu/view.php
	'Modify Preferences'           => 'ModifyPreferencesLink',
	'Change Slot'                  => 'ChangeSlotLink',
	'Reset Look'                   => 'ResetLookLink',
	'Reset Position'               => 'ResetPositionLink',
	'Change Sex'                   => 'ChangeSexLink',
	'Divorce'                      => 'DivorceLink',

	// modules/item/pagemenu/view.php
	'Modify Item'                  => 'ModifyItemLink',
	'Duplicate Item'               => 'DuplicateItemLink',
	'Add to Item Shop'             => 'AddToItemShopLink',
	'Add to Item Shop (Again)'     => 'AddToItemShopAgainLink',

	// modules/account/pagemenu/view.php already calls Flux::message()
	// directly with 'ModifyAccountLink', so it needs no entry here.
);
