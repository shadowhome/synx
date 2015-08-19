<?php
    //Include a generic header
    include 'inc/html/header.php';
    include 'inc/upconfig.php';
    include 'inc/functions.php';

    $servername = (isset($_GET['servername'])) ? $_GET['servername'] : null;
    $ip         = (isset($_GET['ip'])) ? $_GET['ip'] : null;
    $id         = (isset($_GET['id'])) ? $_GET['id'] : null;
    $sshp         = (isset($_GET['sshp'])) ? $_GET['sshp'] : null;

    // Create connection
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    // Check connection
    if (!$conn) {
    	header('Location: /errors/503.php');
    	exit;
    }
?>

    <script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('.select-all').click(function(event) {  //on click
            var strPackage = jQuery(this).data('package');
            if(jQuery(this).prop('checked')) { // check select status
                jQuery('.' + strPackage).each(function() { //loop through each checkbox
                    jQuery(this).prop('checked', true);  //select all checkboxes with class "checkbox"
                });
            } else {
                jQuery('.' + strPackage).each(function() { //loop through each checkbox
                    jQuery(this).prop('checked', false); //deselect all checkboxes with class "checkbox"                      
                });
            }
        });
    });
    </script>

<div class="container">
<div class="page-header">
    <h1 style="text-align: center;">Server Upgrade</h1>
</div>

<?php
echo 'IP:';
print_r($ip);

echo '<br/> <br/>';

echo 'ID:';
print_r ($id);

echo '<br/> <br/>';

echo 'Servername:';
print_r ($servername);

echo '<br/> <br/>';

echo 'SSH Port:';
print_r ($sshp);

echo '<br/> <br/> <br/>';

//echo "<form action='Upgrades.php' method='get'>";
echo "<form id=\"packages\" action='Upgrades.php' method='get'>";
$sql = "SELECT package, id, version, nversion,servername,security,upgrade FROM packages where upgrade = 1 AND servers = $id";
if (isset($_GET['Sec'])) {
	$sql = "SELECT package, id, version, nversion,servername,security,upgrade FROM packages where security = 1 AND servers = $id";
}
elseif(isset($_GET['Check'])) {
	$sql = "SELECT package, id, version, nversion,servername,security,upgrade FROM packages where upgrade = 1 AND servers = $id";
}
$result = $conn->query($sql);
?>
<div class="table-responsive">
<table class="table table-striped">
<thead>
<tr>

<th scope="col"><input type="checkbox" data-package="check-packages" class="select-all" /></th>
<th scope="col">ID</th>
<th scope="col">Package</th>
<th scope="col">ServerName</th>
<th scope="col">Upgrade</th>
<th scope="col">Security</th>
<th scope="col">Version</th>
<th scope="col">Upgrade Version</th>
</tr>
</thead>
<tbody>
<?php
if ($result->num_rows > 0) {

				 while($row = $result->fetch_assoc()) { ?>
					<tr>
						<td><input type="checkbox" name="check-packages[]" value="<?php echo $row['id'];?>" class="check-packages" form="packages"/></td>
						<td><?php echo $row["id"]; ?></td>
						<td><?php echo $row["package"]; ?></td>
						<td><?php echo $row["servername"]; ?></td>
						<td><?php if ($row["upgrade"]==1) {
				 				echo '<span class="glyphicon glyphicon-ok">&nbsp;</span>';
						}
							else {
								echo '<span class="glyphicon glyphicon-remove"></span>';
							}?></td>
						<td><?php if ($row["security"]==1) {
				 				echo '<span class="glyphicon glyphicon-ok">&nbsp;</span>';
						}
							else {
								echo '<span class="glyphicon glyphicon-remove"></span>';
							}?></td>
						<td><?php echo $row["version"]; ?></td>
						<td><?php echo $row["nversion"]; ?></td>
					</tr>
				<?php 
				
				 } ?>
			<?php } else {
					echo "<tr>0 results</tr>";
				}
			?>
			</tbody>
			<?php
			?>
		</table>
	</div>
<?php

if(isset($_GET['Go'])){
	$secid = array();
	$secid = (isset($_GET['sec-packages']))?$_GET['sec-packages']:$_GET['check-packages'];
	print_r($secid);
	$packages = array();
	echo "<p>Your going to upgrade:</p>";
	foreach ($secid as $secpack) {
	$sqln = "SELECT package FROM packages where id = $secpack and servers = $id";
	$resultu = $conn->query($sqln);
    	while ($row = $resultu->fetch_assoc()) {
    		echo "Package:" . $row['package']; echo "<br/>";
    		$packages[] = $row['package'];
    		
    	} 
	}
//	print_r($packages);
	//print_r($row);
	echo "<input type=\"Submit\" name=\"Yes\" value=\"Confirm\">";
	echo "<input type=\"hidden\" name=packs value=\"".implode(" ", $packages)."\">";

}

 
if (isset($_GET['Yes'])) {
	
	$packages = $_GET['packs'];
	
	//exec("ssh sysad@$ip 'export DEBIAN_FRONTEND=noninteractive;sudo apt-get -y install $packages'", $output);
	$cmd = "export DEBIAN_FRONTEND=noninteractive;sudo apt-get -y install $packages; echo $?";
	$output = trim(sshsysad($cmd, $ip, $sshp));
//	echo implode('<br/>',$output);
	flush();
	
	echo nl2br($output);
	$res = (strripos($output,'100') !== strlen($output)-3);
	
	$package = explode(" ", $packages);

	$listSet = '"'.implode('","', $package).'"';
	$changes = "UPDATE Packages SET upgrade = 0, security = 0 WHERE package IN (".$listSet.") AND servers = $id";
	if($res){
	mysqli_query($conn, $changes);
	}
}

mysqli_close($conn);

?>

    <p>What would you like to upgrade?</p>
    <p>
        <input type="hidden" name=id value="<?php echo $id?>">
        <input type="hidden" name=ip value="<?php echo $ip?>">
        <input type="hidden" name=servername value="<?php echo $servername?>">
        <input type="hidden" name=sshp value="<?php echo $sshp?>">
        <input type="submit" class="btn btn-default" name="Check" value="Updates">
        <input type="submit" class="btn btn-default" name="Sec" value="Security">
        <input type="Submit" class="btn btn-default" name="Go" value="Go">
    </p>
</form>
</div>
<?php
    //Include a generic footer
    include 'inc/html/footer.php';
?>
