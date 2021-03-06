<?php
//Include a generic header
include 'inc/html/header.php';
include 'inc/upconfig.php';
$servers = 'SELECT * from servers ORDER BY servername';

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

$result      = $conn->query($servers);
$arrRows = array();

if ($result) {

	while($row = $result->fetch_array()) {
		$arrRows[] = $row;
	}
	$row         = mysqli_fetch_array($result);
	$id          = $row['id'];
	$servername  = array($row['servername']);
	$ip          = $row['ip'];
	$company     = $row['company'];
	$description = $row['description'];
	mysqli_close($conn);
	?>

	<div class="container">
		<div class="page-header">
			<h1 style="text-align: center;">Delete?</h1>
		</div>

		<form action="del-server.php" method="get">
			<?php foreach($arrRows as $row) : ?>
				<input type="radio" name="server" value="<?php echo $row['id']; ?>" /> <?php echo $row['servername']; ?><br />
			<?php endforeach; ?>
			<br />
		  <input class="btn btn-default" type="submit" value="submit" />
		</form>
		<a href="servers.php" class="btn btn-lg btn-link" style="float: right;">Back to Servers</a>
		<br /><br />
	</div>
	<?php
	if(isset($_GET['server'])){
		$server=$_GET['server'];
	// Create connection
	$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	$sql="DELETE FROM servers WHERE servers.id=$server";
	$sql2="DELETE FROM Packages WHERE Packages.servers=$server";

	if (mysqli_query($conn, $sql)&&mysqli_query($conn, $sql2)) {
		echo "New record deleted successfully";
		$serverid=mysqli_insert_id($conn);
		header( "Location: servers.php" );
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}
	mysqli_close($conn);
	}
} else { ?>
	<div class="container">
		<div class="page-header">
			<h1 style="text-align: center;">Delete?</h1>
		</div>
		<span class="alert-warning">No servers Found.</span>
	</div>
<?php }
//Include a generic footer
include 'inc/html/footer.php';