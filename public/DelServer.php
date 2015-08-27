<?php
//Include a generic header
include '../inc/html/header.php';
include '../inc/autoloader.php';

use Synx\Controller\ServerController;

//ToDo: Add Exception handling
$serverController = new ServerController();

if(isset($_GET['server'])){
	$server = $serverController->getServerByID($_GET['server']);
	$serverController->removeServer($server);
}

$servers = $serverController->getServers();

?>

<div class="container">
	<div class="page-header">
		<h1 style="text-align: center;">Delete?</h1>
	</div>

	<form action="DelServer.php" method="get">
	    <?php foreach($servers as $server) : ?>
		    <input type="radio" name="server" value="<?php echo $server->getId(); ?>" /> <?php echo $server->getName(); ?><br />
	    <?php endforeach; ?>
	    <br />
	  <input class="btn btn-default" type="submit" value="submit" />
	</form>
	<a href="Servers.php" class="btn btn-lg btn-link" style="float: right;">Back to Servers</a>
	<br /><br />
</div>
<?php
	//Include a generic footer
	include '../inc/html/footer.php';
?>
