<?php
	//Include a generic header
	include 'inc/html/header.php';
	include 'inc/upconfig.php';


// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT id, package,version,nversion, servers, servername, upgrade, security from Packages where upgrade = 1";

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
			
			
    print '<table border="1">'.PHP_EOL;
    print '<tr>';
    print '<td><input type="checkbox" data-package="check-packages" class="select-all" /></td>';
    print '<td>Package</td>'.PHP_EOL;
    print '</tr>'.PHP_EOL;

 //   $sql = "SELECT package, id, version, nversion FROM packages where upgrade = 1 AND servers = $id";
    $results = $conn->query($sql);

    while($row = $results->fetch_array()) {

        print '<tr>';
        print '<td><input type="checkbox" name="check-packages[]" value="'.$row['id'].'" class="check-packages" /></td>';
        print '<td>'.$row["package"].'</td>';
        print '<td>'.$row["version"].'</td>';
        print '<td>'.$row["nversion"].'</td>';
        print '<td>'.$row["servername"].'</td>';
        print '</tr>'.PHP_EOL;
			}
			
			print '</table>';
			
				$conn->close();
			?>
		</table>
	</div>


			