<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if ($session->isLoggedIn()): ?>
                        <h3 style="font-size: 2rem;"><?php echo htmlspecialchars(Flux::message('LoggedInAsShortLabel')) ?> <?php echo htmlspecialchars($session->account->userid) ?><?php if (count($this->getServerNames()) > 1) echo " " . htmlspecialchars(sprintf(Flux::message('LoggedInOnServerShortLabel'), $session->serverName)); ?></h3>
                        <a href="<?php echo $this->url('account', 'view') ?>" title="<?php echo htmlspecialchars(Flux::message('ViewAccountTitle')) ?>"><?php echo htmlspecialchars(Flux::message('MyAccountLabel')) ?></a> | <a href="<?php echo $this->url('account', 'logout') ?>" onclick="return confirm('Are you sure you want to logout?')"><?php echo htmlspecialchars(Flux::message('LogoutLabel')) ?></a>

<?php endif ?>