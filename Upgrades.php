<!DOCTYPE html>
<html>
<head>
<title>Upgrades to Server</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
</head> 
<body>
<h1>Server Upgrade</h1>
<br>
<script>
jQuery(document).ready(function() {
    jQuery('#cb-package-all').click(function(event) {  //on click
        if(jQuery(this).prop('checked')) { // check select status
            jQuery('.packages').each(function() { //loop through each checkbox
                jQuery(this).prop('checked', true);  //select all checkboxes with class "checkbox"              
            });
        } else {
            jQuery('.packages').each(function() { //loop through each checkbox
                jQuery(this).prop('checked', false); //deselect all checkboxes with class "checkbox"                      
            });        
        }
    });
});
</script>
<?php
include 'inc/upconfig.php';

$servername = $_GET['servername'];
$ip = $_GET['ip'];
$id = $_GET['id'];
print_r('IP:');
print_r ($ip). '<br/>';
echo '<br/>';
print_r('ID:');
print_r ($id). '<br/>';
echo '<br/>';
print_r('Servername:');
print_r ($servername). '<br/>';
echo '<br/>';
echo '<br/>';

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

echo "<form action='Upgrades.php' method='get'>";

if(isset($_GET['Check'])){
	print_r($servername);
	$sql = "SELECT package, id FROM packages where upgrade = 1 AND servers = $id";
	print '<table border="1">'.PHP_EOL;
	print '<tr>';
	print '<td><input id="cb-package-all" type="checkbox" name="packages[]" value="all" /></td>';
	print '<td>Package</td>'.PHP_EOL;
	print '</tr>'.PHP_EOL;
	
	$results = $conn->query($sql);
	while($row = $results->fetch_array()) {
		print '<tr>';
		print '<td><input type="checkbox" name="packages[]" value="'.$row['id'].'" class="packages" /></td>';
		print '<td>'.$row["package"].'</td>';
		print '</tr>'.PHP_EOL;
	
	}
	//exec("ssh root@$ip apt-get -y upgrade 2>&1", $return );
	//var_dump($return);
}
if(isset($_GET['Sec'])){
	print_r($servername);
	$sql = "SELECT package, id FROM packages where security = 1 AND servers = $id";
	print '<table border="1">'.PHP_EOL;
	print '<tr>';
	print '<td><input id="cb-package-all" type="checkbox" name="packages[]" value="all" class="all" /></td>';
	print '<td>Package</td>'.PHP_EOL;
	print '</tr>'.PHP_EOL;
	
	$results = $conn->query($sql);
while($row = $results->fetch_array()) {
    print '<tr>';
	print '<td><input type="checkbox" name="packages[]" value="'.$row['id'].'" class="packages" /></td>';
	print '<td>'.$row["package"].'</td>';
	print '</tr>'.PHP_EOL;

}  
print '</table>';

	//exec("ssh root@$ip apt-get -y upgrade 2>&1", $return );
	//var_dump($return);
}
mysqli_close($conn);

?>

What would you like to upgrade?
<br>
<p>
		
	<input type="hidden" name=id value="<?php echo $id?>">
	<input type="hidden" name=ip value="<?php echo $ip?>">
	<input type="hidden" name=servername value="<?php echo $servername?>">
	<input type="submit" name="Sec" value="Security">
	</form>
	<form action="Upgrades.php" method='get'>
	<input type="hidden" name=id value="<?php echo $id?>">
	<input type="hidden" name=ip value="<?php echo $ip?>">
	<input type="hidden" name=servername value="<?php echo $servername?>">
	<input type="submit" name="Check" value="Updates">
	</form>
	



</body>
</html>




