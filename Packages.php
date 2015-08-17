<?php
	//Include a generic header
	include 'inc/html/header.php';
	include 'inc/upconfig.php';


// NOT USED

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

echo "Packages that are available to be updated";
$sql = "SELECT id, package,version,nversion, servers, servername, upgrade, security from Packages where upgrade = 1";

$result = $conn->query($sql);

?>
<div class="container">
	<div class="page-header">
		<h1 style="text-align: center;">Package Manager</h1>
	</div>

	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
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
			if ($result->num_rows > 0) { ?>
				<?php while($row = $result->fetch_assoc()) { ?>
					<tr>
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
				$conn->close();
			?>
		</table>
	</div>


			