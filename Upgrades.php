<?php

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
<!DOCTYPE html>
<html>
<head>
    <title>Upgrades to Server</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>
    jQuery(document).ready(function() {
        jQuery('.select-all').click(function(event) {  //on click
            var strPackage = jQuery(this).prop('data-package');
            if(jQuery(this).prop('checked')) { // check select status
                jQuery('.' + strPackage').each(function() { //loop through each checkbox
                    jQuery(this).prop('checked', true);  //select all checkboxes with class "checkbox"
                });
            } else {
                jQuery('.' + strPackage').each(function() { //loop through each checkbox
                    jQuery(this).prop('checked', false); //deselect all checkboxes with class "checkbox"                      
                });
            }
        });
    });
    </script>
</head> 
<body>
    <h1 style="padding-bottom: 20px">Server Upgrade</h1>

<?php
echo 'IP:';
print_r($ip)

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
    print '<td><input type="checkbox" data-packages="check-packages" class="select-all" /></td>';
    print '<td>Package</td>'.PHP_EOL;
    print '</tr>'.PHP_EOL;

    $sql = "SELECT package, id FROM packages where upgrade = 1 AND servers = $id";
    $results = $conn->query($sql);

    while($row = $results->fetch_array()) {

        print '<tr>';
        print '<td><input type="checkbox" name="check-packages[]" value="'.$row['id'].'" class="check-packages" /></td>';
        print '<td>'.$row["package"].'</td>';
        print '</tr>'.PHP_EOL;
    }

    print '</table>';
    
    // exec("ssh root@$ip apt-get -y upgrade 2>&1", $return );
    // var_dump($return);
}

if(isset($_GET['Sec'])){

    print '<table border="1">'.PHP_EOL;
    print '<tr>';
    print '<td><input type="checkbox" data-packages="sec-packages" class="select-all" /></td>';
    print '<td>Package</td>'.PHP_EOL;
    print '</tr>'.PHP_EOL;
	
    $sql = "SELECT package, id FROM packages where security = 1 AND servers = $id";
    $results = $conn->query($sql);
    
    while($row = $results->fetch_array()) {

        print '<tr>';
        print '<td><input type="checkbox" name="sec-packages[]" value="'.$row['id'].'" class="sec-packages" /></td>';
        print '<td>'.$row["package"].'</td>';
        print '</tr>'.PHP_EOL;
    }

    print '</table>';

    // exec("ssh root@$ip apt-get -y upgrade 2>&1", $return );
    // var_dump($return);
}

mysqli_close($conn);

?>

        <p>
            What would you like to upgrade?
        </p>
        <p>
            <input type="hidden" name=id value="<?php echo $id?>">
            <input type="hidden" name=ip value="<?php echo $ip?>">
            <input type="hidden" name=servername value="<?php echo $servername?>">
            <input type="submit" name="Check" value="Updates">
            <input type="submit" name="Sec" value="Security">
        </p>
    </form>
</body>
</html>
