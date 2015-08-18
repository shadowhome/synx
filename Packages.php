<?php
	//Include a generic header
	include 'inc/html/header.php';
	include 'inc/upconfig.php';
ini_set('error_reporting', E_ALL);

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT package, id, version, nversion,servername,security,upgrade FROM packages where upgrade = 1";

if (isset($_GET['Sec'])) {
	$sql = "SELECT package, id, version, nversion,servername,security,upgrade FROM packages where security = 1";
	}
elseif(isset($_GET['Check'])) {
	$sql = "SELECT package, id, version, nversion,servername,security,upgrade FROM packages where upgrade = 1";
	}

$result = $conn->query($sql);

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
		<h1 style="text-align: center;">Package Manager</h1>
	</div>

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
						<td><?php echo $row["upgrade"]; ?></td>
						<td><?php echo $row["security"]; ?></td>
						<td><?php echo $row["version"]; ?></td>
						<td><?php echo $row["nversion"]; ?></td>
					</tr>
				<?php } ?>
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
	//print_r($secid);
	$packages = array();
	echo "<p>Your going to upgrade:</p>";
	foreach ($secid as $secpack) {
		$sqln = "SELECT package FROM packages where id = $secpack";
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
if(isset($_GET['Yes'])){
	$packages = $_GET['packs'];
	//$package = implode(" ", $packages);
	//		print_r($packages);
	exec("ssh sysad@$ip 'export DEBIAN_FRONTEND=noninteractive;sudo apt-get -y install $packages'", $output);
	echo implode('<br/>',$output);
	//		$packages = array();
	$package = explode(" ", $packages);
	//		print_r($package);

	foreach ($package as $setu) {
		//$setHist = "INSERT INTO PackagesHist (package,version,servers,servername,upgraded) VALUES ($setu,$version,$id,$servername,\"".date('Y-m-d')."\"";
		$uphist = "insert into packageHist (package, version, servers, servername, upgraded) select packages.package, packages.version, servers.id, servers.servername, \"".date('Y-m-d')."\" from packages inner join servers on packages.servers = servers.id where packages.package in ('implode(',',$packages)')";
		$listSetu = '"'.implode('","', $package).'"';
		$changeu = "UPDATE Packages SET upgrade = 0, security = 0 WHERE package IN (".$listSetu.") AND servers = $id";
		//print_r($changeu);
		mysqli_query($conn, $changeu)&&mysqli_query($conn, $changes)&&mysqli_query($conn, $uphist);
	}

}

mysqli_close($conn);


?>
<form id="packages" action='Packages.php' method='get'>
    <p>What would you like to upgrade?</p>
    <p>

 

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
			