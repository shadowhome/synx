<?php
include '../inc/autoloader.php';
//Include a generic header
include '../inc/html/header.php';
include '../inc/upconfig.php';
include '../inc/functions.php';

use Synx\Controller\ServerController;
use Synx\Controller\CompanyController;
use Synx\Controller\OperatingSystemController;
use Synx\Controller\OperatingSystemVersionController;

$serverController = new ServerController();
$companyController = new CompanyController();
$operatingSystemController = new OperatingSystemController();
$operatingSystemVersionController = new OperatingSystemVersionController();

$servers = $serverController->getServers();

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
			if (!empty($servers)):
				foreach($servers as $server):
					$company = $companyController->getCompanyById($server->getCompanyId());
					$operatingSystemVersion = $operatingSystemVersionController->getOperatingSystemVersionByID($server->getOsVersionId());
					$operatingSystem = $operatingSystemController->getOperatingSystemByID($operatingSystemVersion->getOsId());
			?>
				<tr>
					<td><?php echo $server->getId(); ?></td>
					<td><a href="Servers.php?id=<?php echo $server->getId(); ?>#server<?php echo $server->getId(); ?>"><?php echo $server->getName(); ?></a></td>
					<td><?php echo $server->getIp(); ?></td>
					<td><?php echo $company->getName(); ?></td>
					<td><?php echo $operatingSystem->getName(); ?></td>
					<td><?php echo $operatingSystemVersion->getName(); ?></td>
					<td><?php echo $operatingSystemVersion->getCode(); ?></td>
					<td><?php echo $server->getDescription(); ?></td>
				</tr>
			<?php
				endforeach;
			else:
			?>
				<tr>
					<td colspan="8">No Results Found</td>
				</tr>
			<?php
			endif;
			?>
			</tbody>
		</table>
	</div>

	<div class="row">
		<div class="col-md-6">
			<h2><center><a href="NewServer.php" class="label label-primary">New Server</a></center></h2>
		</div>
		<div class="col-md-6">
			<h2><center><a href="DelServer.php" class="label label-danger">Delete Server</a></center></h2>
		</div>
	</div>

	<br />

	<div class="panel panel-success">
		<div class="panel-heading">
			<h3 class="panel-title">Search Server Details</h3>
			<p>You may search either by Servername or IP</p>
		</div>
		<div class="panel-body">
		    <form method="post" action="Servers.php?go" id="searchform">
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
	if(isset($_GET['id'])) {
		//ToDo: Exception Handling
		$server = $serverController->getServerByID($_GET['id']);
		$company = $companyController->getCompanyByID($server->getCompanyId());
		$os_version = $operatingSystemVersionController->getOperatingSystemVersionByID($server->getOsVersionId());
		$os = $operatingSystemController->getOperatingSystemByID($os_version->getOsId());

		$updatesOnly = true;
		$secOnly     = false;

		if (isset($_REQUEST['sec']) && $_REQUEST['sec'] === '1') {
			$secOnly     = true;
		}

		if (isset($_REQUEST['updates']) && $_REQUEST['updates'] !== '1') {
			$updatesOnly = false;
		}

		?>
		<a name="server<?php echo $server->getId();?>" style="text-decoration: none; color: black;">
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
						<td><?php echo $server->getId(); ?></td>
						<td><?php echo $server->getName(); ?></td>
						<td><?php echo $server->getIp(); ?></td>
						<td><?php echo $server->getPort(); ?></td>
						<td><?php echo $company->getName(); ?></td>
						<td><?php echo $os->getName(); ?></td>
						<td><?php echo $os_version->getName(); ?></td>
						<td><?php echo $os_version->getCode(); ?></td>
						<td><?php echo $server->getDescription(); ?></td>
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
						<td><?php echo $server->getCpuNumber(); ?></td>
						<td><?php echo $server->getCpuThreads(); ?></td>
						<td><?php echo $server->getCpuCore(); ?></td>
						<td><?php echo $server->getCpuFrequency(); ?></td>
						<td><?php echo $server->getCpuArchitecture(); ?></td>
						<td><?php echo $server->getCpuSockets(); ?></td>
						<td><?php echo $server->getRam(); ?></td>

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
				    <input type="text" class="form-control" id="servername" name="servername" value="<?php echo $server->getName();?>">
				  </div>
				  <div class="form-group">
				    <label for="company">Company</label>
				    <input type="text" class="form-control" id="company" name="company" value="<?php echo $company->getName();?>">
				  </div>
				  <div class="form-group">
				    <label for="description">Description</label>
				    <textarea type="text" class="form-control" id="description" name="description" rows="5" cols="40"><?php echo $server->getDescription();?></textarea>
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
				<form action="Servers.php" method='get'>
					<input type="hidden" name=id value="<?php echo $id?>">
					<input type="hidden" name="updates" value="1"/>
					<input type="submit" class="btn btn-md btn-success" name="Check" value="Show updates only">
				</form>
			</div>
			<div class="col-md-2" style="margin-bottom: 20px;">
				<form action="Servers.php" method='get'>
					<input type="hidden" name=id value="<?php echo $id?>" />
					<input type="hidden" name="updates" value="0" />
					<input type="submit" class="btn btn-md btn-info" name="Check" value="Show All">
				</form>
			</div>
			<div class="col-md-2" style="margin-bottom: 20px;">
				<form action="Servers.php" method='get'>
					<input type="hidden" name=id value="<?php echo $id?>">
					<input type="hidden" name="sec" value="1"/>
					<input type="submit" class="btn btn-md btn-warning" name="Check" value="Show Security updates only">
				</form>
			</div>
			<div class="col-md-2" style="margin-bottom: 20px;">
				<form action="Upgrades.php" method='get'>
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
</div>
<?php
	//Include a generic footer
	include '../inc/html/footer.php';
