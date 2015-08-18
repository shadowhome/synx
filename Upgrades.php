<?php
    //Include a generic header
    include 'inc/html/header.php';
    include 'inc/upconfig.php';

    $servername = (isset($_GET['servername'])) ? $_GET['servername'] : null;
    $ip         = (isset($_GET['ip'])) ? $_GET['ip'] : null;
    $id         = (isset($_GET['id'])) ? $_GET['id'] : null;

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

echo '<br/> <br/> <br/>';

echo "<form action='Upgrades.php' method='get'>";

if(isset($_GET['Check'])){

    print '<table border="1">'.PHP_EOL;
    print '<tr>';
    print '<td><input type="checkbox" data-package="check-packages" class="select-all" /></td>';
    print '<td>Package</td>'.PHP_EOL;
    print '</tr>'.PHP_EOL;

    $sql = "SELECT package, id, version, nversion,servername FROM packages where upgrade = 1";
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
//    $version = $row['version'];
    
    // exec("ssh root@$ip apt-get -y upgrade 2>&1", $return );
    // var_dump($return);
}

if(isset($_GET['Sec'])){

    print '<table border="1">'.PHP_EOL;
    print '<tr>';
    print '<td><input type="checkbox" data-package="sec-packages" class="select-all" /></td>';
    print '<td>Package</td>'.PHP_EOL;
    print '</tr>'.PHP_EOL;
	
    $sql = "SELECT package, id, version, nversion,servername FROM packages where security = 1";
    $results = $conn->query($sql);
    
    while($row = $results->fetch_array()) {

        print '<tr>';
        print '<td><input type="checkbox" name="sec-packages[]" value="'.$row['id'].'" class="sec-packages" /></td>';
        print '<td>'.$row["package"].'</td>';
        print '</tr>'.PHP_EOL;
    }

    print '</table>';
 

}
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
	
	exec("ssh sysad@$ip 'export DEBIAN_FRONTEND=noninteractive;sudo apt-get -y install $packages'", $output);
	echo implode('<br/>',$output);
	$package = explode(" ", $packages);

	$listSet = '"'.implode('","', $package).'"';
	$changes = "UPDATE Packages SET upgrade = 0, security = 0 WHERE package IN (".$listSet.") AND servers = $id";
	mysqli_query($conn, $changes);
}

mysqli_close($conn);

?>
    <p>What would you like to upgrade?</p>
    <p>
        <input type="hidden" name=id value="<?php echo $id?>">
        <input type="hidden" name=ip value="<?php echo $ip?>">
        <input type="hidden" name=servername value="<?php echo $servername?>">
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
