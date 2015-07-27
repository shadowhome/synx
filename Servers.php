<?php
include 'inc/upconfig.php';
$servers = 'SELECT * from servers ORDER BY servername';

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}
$result = $conn->query($servers);
if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {

		echo "id: " . $row["id"]. 
		" - Name: " . "<a  href=\"Servers.php?id=".$row['id']."\">" . $row["servername"] .  "</a>".
		" - IP: " . $row["ip"].
		" - Company: " . $row["company"].
		" - OS: " . $row["OS"].
		" - Version: " . $row["version"].
		" - Release: " . $row['releasever'].
		" - Description: " . $row["description"].
		"\n".
		"<br>";
	}
} else {
	echo "0 results";
}
$conn->close();
?>
<a href="NewServer.php">New Server</a>
<a href="DelServer.php">Delete Server</a>
    <h4>Search Server Details</h4>
    <p>You may search either by Servername or IP</p>
    <form  method="post" action="Servers.php?go"  id="searchform">
      <input  type="text" name="servername">
      <input type="text" name="ip">
      <input  type="submit" name="submit" value="Search">
    </form>
    
   <br><br>
<?php
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}
if(isset($_GET['id'])){
	$id = $_GET['id'];
	$updatesOnly = (isset($_REQUEST['updates']) && $_REQUEST['updates']==='1');
	$secOnly = (isset($_REQUEST['sec']) && $_REQUEST['sec']==='1');
	//echo ($updatesOnly)?'updates: yes':'updates: no';
	$sql="SELECT  id, servername, ip, company, version, OS, description, releasever FROM servers WHERE id = '$id'";
	$result=mysqli_query($conn, $sql);
	$row=mysqli_fetch_array($result);
	$packs="SELECT package, OS, version, upgrade, security from packages where servers = '$id'".(($updatesOnly)?' AND upgrade="1"':'').(($secOnly)?' AND security="1"':'');
	
	
	$resultp=mysqli_query($conn, $packs);
	$id=$row['id'];
	$servername=$row['servername'];
	$ip=$row['ip'];
	$company=$row['company'];
	$description=$row['description'];
	echo "SERVER DETAILS"."\n"."<br>";
	echo "id: " . $row["id"]. 
	" - Name: " . $row["servername"] .
	" - IP: " . $row["ip"].
	" - Company: " . $row["company"].
	" - OS: " . $row["OS"].
	" - Version: " . $row["version"].
	" - Release: " . $row['releasever'].
	" - Description: " . $row["description"].
	"\n".
	"<br>" ;
	?>
	<br>
	
	Update Server details:
	<form action="new.php" method="post"><p>
	ServerName: <input type="text" name="servername" value="<?php echo $servername;?>" id='servername'><br>
	
	Company: <input type="text" name="company" value="<?php echo $company;?>" id=company><br>
	
	Description: <textarea name="description" rows="5" cols="40"><?php echo $description;?></textarea>
	<br>
	<input type="submit">
	</form>
	
	<br>
	<form action="packs.php" method='get'>
	<input type="hidden" name=id value="<?php echo $id?>">
	<input type="hidden" name=ip value="<?php echo $ip?>">
	<input type="submit" name="Check" value="Check for updates">
	</form>
	<form action="Servers.php" method='get'>
	<input type="hidden" name=id value="<?php echo $id?>">
	<input type="hidden" name="updates" value="1"/>
	<input type="submit" name="Check" value="Show updates only">
	</form>
	<form action="Servers.php" method='get'>
	<input type="hidden" name=id value="<?php echo $id?>">
	<input type="submit" name="Check" value="Show All">
	</form>
	<form action="Servers.php" method='get'>
	<input type="hidden" name=id value="<?php echo $id?>">
	<input type="hidden" name="sec" value="1"/>
	<input type="submit" name="Check" value="Show Security updates only">
	</form>
	<form action="Upgrades.php" method='get'>
	<input type="hidden" name=id value="<?php echo $id?>">
	<input type="hidden" name=servername value="<?php echo $servername?>">
	<input type="hidden" name=ip value="<?php echo $ip?>">
	<input type="submit" name="Up" value="Upgrades">
	</form>

<?php	
	//$resultp=mysqli_query($conn, $packs);
	//print_r($resultp);
	//print_r('andy');
	//print_r($packs);

	print '<table border="1">';
	print '<th>ID</th>';
	print '<th>Package</th>';
	print '<th>Version</th>';
	print '<th>Upgradeable</th>';
	print '<th>Security</th>';
		
	//while($row1=mysqli_fetch_array($resultp)) {
	while($row1 = $resultp->fetch_assoc()) {
		//print_r($resultp); 
		print '<tr>';
		//print '<td>'.$row1["servers"].'</td>';
		print '<td>'.$id;
		print '<td>'.$row1["package"].'</td>';
		print '<td>'.$row1["version"].'</td>';
		print '<td>'.$row1["upgrade"].'</td>';
		print '<td>'.$row1["security"].'</td>';
		print '</tr>';
		
		//print_r($row1);
		//echo "List of Installed Packages:". 
		//" - Package: " . $row1["package"].
		//" - Version: " . $row1["version"].
		//" - Upgradeable: " . $row1["upgrade"].
		//" - Security: " . $row1["security"].
		//"\n".
		//"<br>"; 
	}
	print '</table>';

}



//  if(isset($_POST['submit'])){
//  if(isset($_GET['go'])){
  	 

//   $servername = mysqli_real_escape_string($conn, $_POST['servername']);
//   $ip = mysqli_real_escape_string($conn, $_POST['ip']);
   
 //  $sql="SELECT ID, servername, ip FROM servers WHERE servername LIKE '%" . $servername . "%'";
//   $sql="SELECT  id, servername, ip FROM servers WHERE servername LIKE '%" . $servername .  "%' OR ip LIKE '%" . $ip ."%'";
//   $result=mysqli_query($conn, $sql);
//   $numrows=mysqli_num_rows($result);
   //
//   echo  "<p>" .$numrows . " results found </p>";
   
//   while($row=mysqli_fetch_array($result)){
//   	$servername  = $row['servername'];
//   	$ID=$row['id'];
//   	//-display  the result of the array
//   	echo  "<ul>\n";
//   	echo  "<li>" . "<a  href=\"Servers.php?id=$ID\">"   .$servername . " " .  "</a></li>\n";
//   	echo  "</ul>";
//   	
//  }
//  }
//  else{
//  echo  "<p>Please enter a search query</p>";
//  }
  mysqli_close($conn);
//}
?>

  