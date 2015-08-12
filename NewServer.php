<?php
	//Include a generic header
	include 'inc/html/header.php';
?>
<div class="page-header">
	<h1 style="text-align: center;">Add New Server</h1>
</div>
<form action="new.php" method="post">
	<div class="form-group">
		<label for="servername">Server Name</label>
		<input type="text" class="form-control" id="servername" name="servername" placeholder="servername">
	</div>
	<div class="form-group">
		<label for="ip">IP</label>
		<input type="text" class="form-control" id="ip" name="ip" placeholder="ip">
	</div>
	<div class="form-group">
		<label for="sshp">SSH Port</label>
		<input type="text" class="form-control" id="sshp" name="sshp" placeholder="<?php echo '22';?>">
	</div>
	<div class="form-group">
		<label for="company">Company</label>
		<input type="text" class="form-control" id="company" name="company" placeholder="company">
	</div>
	<div class="form-group">
		<label for="populate">Populate</label>
			<div class="radio">
			  <label>
			    <input type="radio" name="populate" <?php if (isset($populate) && $populate=="yes") echo "checked";?> value="yes" id='yes'>
			    Yes
			  </label>
			</div>
			<div class="radio">
			  <label>
			    <input type="radio" name="populate" <?php if (isset($populate) && $populate=="no") echo "checked";?> value="no" id='no'>
			    No
			  </label>
			</div>
	</div>
	<div class="form-group">
		<label for="description">Description</label>
		<textarea name="description" class="form-control" rows="5" cols="40"><?php echo "description";?></textarea>
	</div>
	<div class="form-group">
		<label for="pass">Root Password</label>
		<input type="text" class="form-control" id="pass" name="pass" placeholder="password">
	</div>
	<p style="font-size: 11px; font-style: italic;">This is used to create the nessesary script and to setup an unprivileged user on remote with access using ssh-key from origin server</p>

	<input class="btn btn-default" type="submit">

</form>
<br />
<div class="well">
	<p>Have you added the web servers ssh-key to the remote root account /root/.ssh/authorized_keys, if not please do so now! Also please check you can ssh as root the your server without the security check, you will need to access the ip not the servername
	Please ensure PermitRootLogin without-password is not set in sshd_config or else it will fail to login</p>
</div>
<?php
	//Include a generic footer
	include 'inc/html/footer.php';
?>