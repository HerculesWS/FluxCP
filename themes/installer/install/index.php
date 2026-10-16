<?php if (!$session->installerAuth): ?>
	<form action="<?php echo htmlspecialchars((string)$this->url) ?>" method="post" class="card form-stack">
		<?php echo Flux_Security::csrfGenerate('InstallerLogin', true) ?>
		<p class="lead">
			Please enter your <em>installer password</em> to continue with the update.
		</p>
		<div class="form-row">
			<label for="installer_password">Password</label>
			<input type="password" id="installer_password" name="installer_password" autocomplete="current-password" />
		</div>
		<div>
			<button type="submit" class="btn-primary">Authenticate</button>
		</div>
	</form>
<?php else: ?>
	<?php if (isset($permissionError)): ?>
		<section class="card">
		<h2 class="error">MySQL Permission Error Encountered</h2>
		<p>Uh oh, the installer encountered a permission error while trying to execute one of the schema definitions!</p>
		<p>This typically means that the query failed due to lack of user/database/table permissions in MySQL.</p>
		<div class="table-wrap">
		<table class="schema-info permission">
			<!--
			<tr>
				<th>Schema Type</th>
				<td><?php echo $permissionError->isLoginDbSchema() ? 'Login Server Database' : 'Char/Map Server Database' ?></td>
			</tr>
			<tr>
				<th>Schema File</th>
				<td><?php echo htmlspecialchars(realpath($permissionError->schemaFile)) ?></td>
			</tr>
			-->
			<tr>
				<th>Server</th>
				<td>
					<?php echo htmlspecialchars((string)$permissionError->mainServerName) ?>
					<?php if ($permissionError->charMapServerName): ?>
						(<?php echo htmlspecialchars((string)$permissionError->charMapServerName) ?>)
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<th>Database</th>
				<td><?php echo htmlspecialchars((string)$permissionError->databaseName) ?></td>
			</tr>
			<tr>
				<th>Error</th>
				<td><?php echo htmlspecialchars($permissionError->getMessage()) ?></td>
			</tr>
			<tr>
				<th>SQL Query</th>
				<td><code><?php echo nl2br(htmlspecialchars((string)$permissionError->query)) ?></code></td>
			</tr>
		</table>
		</div>
		<div class="callout">
			<p><strong>The recommended solution to a problem like this is to grant the user the the privileges to
				run the query on the database or table.</strong></p>
			<p><strong>Manually running the SQL query is not a supported method because schema versioning will break
				and the installer will not go away.</strong></p>
		</div>
		</section>
	<?php else: ?>
		<div class="toolbar">
			<div class="toolbar-actions">
				<a class="btn btn-primary" href="<?php echo htmlspecialchars($this->url($params->get('module'), null, array('update_all' => 1, 'Session' => Flux_Security::csrfGet('Session')))) ?>" onclick="return confirm('By performing this action, changes to your database will be made.\n\nAre you sure you want to continue installing Flux and its associated updates?')">Install or Update Everything</a>
			</div>
			<a class="btn" href="<?php echo htmlspecialchars($this->url($params->get('module'), null, array('logout' => 1))) ?>" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
		</div>
		<p class="hint">"Install or Update Everything" will use the pre-configured MySQL username and password for each server.</p>
		<p class="hint">Shown below is a list of currently installed / need-to-be-installed schemas.</p>
		<form action="<?php echo htmlspecialchars((string)$this->urlWithQs) ?>" method="post">
		<?php echo Flux_Security::csrfGenerate('InstallerUpdate', true) ?>
			<?php foreach ($installer->servers as $mainServerName => $mainServer): ?>
			<?php $servName = base64_encode($mainServerName) ?>
			<section class="card">
				<div class="server-head">
					<h2 class="server-title"><?php echo htmlspecialchars((string)$mainServerName) ?></h2>
					<button type="submit" class="btn-primary" name="update[<?php echo $servName ?>]">
						Update <?php echo htmlspecialchars((string)$mainServerName) ?>
					</button>
				</div>

				<details class="alt-credentials">
					<summary>Alternative MySQL username/password</summary>
					<div class="form-stack">
						<div class="form-row">
							<label for="username_<?php echo $servName ?>">MySQL username</label>
							<input class="input" type="text" name="username[<?php echo $servName ?>]" id="username_<?php echo $servName ?>" autocomplete="off" />
						</div>
						<div class="form-row">
							<label for="password_<?php echo $servName ?>">MySQL password</label>
							<input class="input" type="password" name="password[<?php echo $servName ?>]" id="password_<?php echo $servName ?>" autocomplete="off" />
						</div>
					</div>
				</details>

				<div class="table-wrap">
				<table class="schema-info">
					<thead>
						<tr>
							<th>Schema Name</th>
							<th>Latest Version</th>
							<th>Version Installed</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($mainServer->schemas as $schema): ?>
						<tr>
							<td>
								<span class="pill <?php echo ($schema->versionInstalled == $schema->latestVersion) ? 'uptodate' : 'needtoupdate' ?>">
									<?php echo htmlspecialchars((string)$schema->schemaInfo['name']) ?>
								</span>
							</td>
							<td>
								<?php if ($schema->latestVersion > $schema->versionInstalled): ?>
									<span class="schema-query" title="<?php echo htmlspecialchars(file_get_contents($schema->schemaInfo['files'][$schema->latestVersion])) ?>">
									<?php echo htmlspecialchars((string)$schema->latestVersion) ?>
									</span>
								<?php else: ?>
									<?php echo htmlspecialchars((string)$schema->latestVersion) ?>
								<?php endif ?>
							</td>
							<td><?php echo $schema->versionInstalled ? htmlspecialchars((string)$schema->versionInstalled) : '<span class="none">None</span>' ?></td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
				</div>

				<?php foreach ($mainServer->charMapServers as $charMapServerName => $charMapServer): ?>
				<h3 class="sub-head"><?php echo htmlspecialchars((string)$charMapServerName) ?></h3>
				<div class="table-wrap">
				<table class="schema-info">
					<thead>
						<tr>
							<th>Schema Name</th>
							<th>Latest Version</th>
							<th>Version Installed</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($charMapServer->schemas as $schema): ?>
						<tr>
							<td>
								<span class="pill <?php echo ($schema->versionInstalled == $schema->latestVersion) ? 'uptodate' : 'needtoupdate' ?>">
									<?php echo htmlspecialchars((string)$schema->schemaInfo['name']) ?>
								</span>
							</td>
							<td><?php echo htmlspecialchars((string)$schema->latestVersion) ?></td>
							<td><?php echo $schema->versionInstalled ? htmlspecialchars((string)$schema->versionInstalled) : '<span class="none">None</span>' ?></td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
				</div>
				<?php endforeach ?>
			</section>
			<?php endforeach ?>
		</form>
	<?php endif ?>
<?php endif ?>
