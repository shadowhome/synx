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
$result = $conn->query($servers);
?>

    <script type="text/javascript">
    $(document).ready(function() 
    	    { 
    	        $("#myTable").tablesorter(); 
    	    } 
    	); 
    </script>
    
<div class="container">
	<div class="page-header">
		<h1 style="text-align: center;">Server Manager</h1>
	</div>

	<div class="table-responsive">
		<table class="table table-striped sortable">
			<thead>
				<tr>
					<th scope="col">ID</th>
					<th scope="col">Name</th>
					<th scope="col">IP</th>
					<th scope="col">Company</th>
					<th scope="col">OS</th>
					<th scope="col">Version</th>
					<th scope="col">Release</th>
					<th scope="col">Description</th>
				</tr>
			</thead>
			<tbody>
			<?php

			if (!empty($result) && $result->num_rows > 0) { ?>
				<?php while($row = $result->fetch_assoc()) { ?>
					<tr>
						<td><?php echo $row["id"]; ?></td>
						<td><a href="servers.php?id=<?php echo $row['id']; ?>#server<?php echo $row['id']; ?>"><?php echo $row["servername"]; ?></a></td>
						<td><?php echo $row["ip"]; ?></td>
						<td><?php echo $row["company"]; ?></td>
						<td><?php echo $row["OS"]; ?></td>
						<td><?php echo $row["version"]; ?></td>
						<td><?php echo $row["releasever"]; ?></td>
						<td><?php echo $row["description"]; ?></td>
					</tr>
				<?php } ?>
			<?php } else {
					echo '<tr><td colspan="8" class="alert-warning">0 results</td></tr>';
				}
			?>
			</tbody>
			<?php
				$conn->close();
			?>
		</table>
	</div>

	<div class="row">
		<div class="col-md-6">
			<h2><center><a href="new-server.php" class="label label-primary">New Server</a></center></h2>
		</div>
		<div class="col-md-6">
			<h2><center><a href="del-server.php" class="label label-danger">Delete Server</a></center></h2>
		</div>
	</div>

	<br />

	<div class="panel panel-success">
		<div class="panel-heading">
			<h3 class="panel-title">Search Server Details</h3>
			<p>You may search either by Servername or IP</p>
		</div>
		<div class="panel-body">
		    <form method="post" action="servers.php?go" id="searchform">
			  <div class="form-group">
			    <label for="servername">Server Name</label>
			    <input type="text" class="form-control" id="servername" name="servername" placeholder="Server Name">
			  </div>
			  <div class="form-group">
			    <label for="ip">IP</label>
			    <input type="text" class="form-control" id="ip" name="ip" placeholder="IP">
			  </div>
		      <input class="btn btn-default" type="submit" name="submit" value="Search">
		    </form>
		</div>
	</div>

	   
	<?php
	$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	if(isset($_GET['id'])) {
		$id          = $_GET['id'];

		$updatesOnly = true;
		$secOnly     = false;

		if (isset($_REQUEST['sec']) && $_REQUEST['sec'] === '1') {
			$secOnly     = true;
		}

		if (isset($_REQUEST['updates']) && $_REQUEST['updates'] !== '1') {
			$updatesOnly = false;
		}

		$sql = "SELECT  id, servername, ip, company, version, OS, description, releasever, sshp, CPUF,CPUArch,CPUNo,CPUSockets,CPUThreads,CPUC,RAM ".
			   "FROM servers WHERE id = '$id'";

		$result = mysqli_query($conn, $sql);
		$row    = mysqli_fetch_array($result);

		$packs  = "SELECT package, OS, version, upgrade, security, changelog, date, rc, ii, md5 ".
				  "FROM Packages ".
				  "WHERE ".
					"servers = '$id' ".
					(($updatesOnly)?' AND upgrade="1"':'').
					(($secOnly)?' AND security="1"':'');
		
		
		$resultp     = mysqli_query($conn, $packs);
		$id          = $row['id'];
		$servername  = $row['servername'];
		$ip          = $row['ip'];
		$company     = $row['company'];
		$description = $row['description'];
		$sshp        = $row['sshp']; 
		$cpuf          	= $row['CPUF'];
		$cpua  			= $row['CPUArch'];
		$cpun          	= $row['CPUNo'];
		$cpus	     	= $row['CPUSockets'];
		$cput		 	= $row['CPUThreads'];
		$cpuc		 	= $row['CPUC'];
		$ram		 	= $row['RAM'];
		

		
		
		?>
		<a name="server<?php echo $row['id'];?>" style="text-decoration: none; color: black;">
			<h1 style="text-align: center;">SERVER DETAILS</h1>
		</a>

		<div class="table-responsive">
			<table class="table table-striped sortable">
				<thead>
					<tr>
						<th scope="col">ID</th>
						<th scope="col">Name</th>
						<th scope="col">IP</th>
						<th scope="col">SSH Port</th>
						<th scope="col">Company</th>
						<th scope="col">OS</th>
						<th scope="col">Version</th>
						<th scope="col">Release</th>
						<th scope="col">Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo $row["id"]; ?></td>
						<td><?php echo $row["servername"]; ?></td>
						<td><?php echo $row["ip"]; ?></td>
						<td><?php echo $row["sshp"]; ?></td>
						<td><?php echo $row["company"]; ?></td>
						<td><?php echo $row["OS"]; ?></td>
						<td><?php echo $row["version"]; ?></td>
						<td><?php echo $row["releasever"]; ?></td>
						<td><?php echo $row["description"]; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		
		
				<div class="table-responsive">
			<table class="table table-striped sortable">
				<thead>
					<tr>
						<th scope="col">No Cpus</th>
						<th scope="col">CPU Threads</th>
						<th scope="col">CPU Cores</th>
						<th scope="col">CPU Freq</th>
						<th scope="col">CPU Arch</th>
						<th scope="col">CPU Sockets</th>
						<th scope="col">RAM</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo $cpun; ?></td>
						<td><?php echo $cput; ?></td>
						<td><?php echo $cpuc; ?></td>
						<td><?php echo $cpuf; ?></td>
						<td><?php echo $cpua; ?></td>
						<td><?php echo $cpus; ?></td>
						<td><?php echo $ram; ?></td>

					</tr>
				</tbody>
			</table>
		</div>
		
		
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h2 class="panel-title">Update Server details:</h2>
			</div>
			<div class="panel-body">
				<form action="new.php" method="post">
				  <div class="form-group">
				    <label for="servername">Server Name</label>
				    <input type="text" class="form-control" id="servername" name="servername" value="<?php echo $servername;?>">
				  </div>
				  <div class="form-group">
				    <label for="company">Company</label>
				    <input type="text" class="form-control" id="company" name="company" value="<?php echo $company;?>">
				  </div>
				  <div class="form-group">
				    <label for="description">Description</label>
				    <textarea type="text" class="form-control" id="description" name="description" rows="5" cols="40"><?php echo $description;?></textarea>
				  </div>
					<input class="btn btn-default" type="submit">
				</form>    
			</div>
		</div>

		<br/>

		<div class="row" style="text-align: center;">
			<div class="col-md-2" style="margin-bottom: 20px;">
				<form action="packs.php" method='get'>
					<input type="hidden" name=id value="<?php echo $id?>">
					<input type="hidden" name=ip value="<?php echo $ip?>">
					<input type="hidden" name=servername value="<?php echo $servername?>">
					<input type="hidden" name=company value="<?php echo $company?>">
					<input type="hidden" name=sshp value="<?php echo $sshp?>">
					<input type="submit" class="btn btn-md btn-primary" name="Check" value="Check for updates">
				</form>
			</div>
			<div class="col-md-2" style="margin-bottom: 20px;">
				<form action="servers.php" method='get'>
					<input type="hidden" name=id value="<?php echo $id?>">
					<input type="hidden" name="updates" value="1"/>
					<input type="submit" class="btn btn-md btn-success" name="Check" value="Show updates only">
				</form>
			</div>
			<div class="col-md-2" style="margin-bottom: 20px;">
				<form action="servers.php" method='get'>
					<input type="hidden" name=id value="<?php echo $id?>" />
					<input type="hidden" name="updates" value="0" />
					<input type="submit" class="btn btn-md btn-info" name="Check" value="Show All">
				</form>
			</div>
			<div class="col-md-2" style="margin-bottom: 20px;">
				<form action="servers.php" method='get'>
					<input type="hidden" name=id value="<?php echo $id?>">
					<input type="hidden" name="sec" value="1"/>
					<input type="submit" class="btn btn-md btn-warning" name="Check" value="Show Security updates only">
				</form>
			</div>
			<div class="col-md-2" style="margin-bottom: 20px;">
				<form action="upgrades.php" method='get'>
					<input type="hidden" name=id value="<?php echo $id?>">
					<input type="hidden" name=servername value="<?php echo $servername?>">
					<input type="hidden" name=ip value="<?php echo $ip?>">
					<input type="hidden" name=sshp value="<?php echo $sshp?>">
					<input type="submit" class="btn btn-md btn-default" name="Up" value="Upgrades"> 
				</form>
			</div>
					<div class="col-md-2" style="margin-bottom: 20px;">
					<form action="packs.php" method='get'>
					<input type="hidden" name=id value="<?php echo $id?>">
					<input type="hidden" name=ip value="<?php echo $ip?>">
					<input type="hidden" name=servername value="<?php echo $servername?>">
					<input type="hidden" name=sshp value="<?php echo $sshp?>">
					<input type="hidden" name=company value="<?php echo $company?>">
					<input type="submit" class="btn btn-md btn-danger" name="Cron" value="Update cron">
				</form>
			</div>
		</div>

		<div class="table-responsive">
			<table class="table table-striped sortable">
				<thead>
					<tr>
						<th scope="col">ID</th>
						<th scope="col">Package</th>
						<th scope="col">Version</th>
						<th scope="col">Upgradeable</th>
						<th scope="col">Security</th>
						<th scope="col">Changelog</th>
						<th scope="col">Date Installed</th>
						<th scope="col">rc</th>
						<th scope="col">ii</th>
						<th scope="col">md5</th>
					</tr>
				</thead>
				<tbody>
				<?php	
					
					while($row1 = $resultp->fetch_assoc()) {
						print '<tr>';
						print '<td>'.$id;
						print '<td>'.$row1["package"].'</td>';
						print '<td>'.$row1["version"].'</td>';
						print '<td>'.$row1["upgrade"].'</td>';
						print '<td>'.$row1["security"].'</td>';
						print '<td>'.nl2br($row1["changelog"]).'</td>';
						print '<td>'.$row1["date"].'</td>';
						print '<td>'.$row1["rc"].'</td>';
						print '<td>'.$row1["ii"].'</td>';
						print '<td>'.$row1["md5"].'</td>';
						print '</tr>';
					} ?>
				</tbody>
			</table>
		</div>
	<?php } ?>
	<?php


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
	//   	echo  "<li>" . "<a  href=\"servers.php?id=$ID\">"   .$servername . " " .  "</a></li>\n";
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
</div>
<?php
	//Include a generic footer
	include 'inc/html/footer.php';
