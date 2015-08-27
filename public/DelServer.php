<?php
//Include a generic header
include 'inc/html/header.php';
include 'inc/upconfig.php';
$servers = 'SELECT * from servers ORDER BY servername';

//ToDo: Change Classes to autoload
include __DIR__.'/classes/Server.php';
include __DIR__.'/classes/ServerController.php';

use Synx\Controller\ServerController;

$serverController = new ServerController();
$servers = $serverController->getServers();
?>

<div class="container">
	<div class="page-header">
		<h1 style="text-align: center;">Delete?</h1>
	</div>

	<form action="DelServer.php" method="get">
	    <?php foreach($arrRows as $row) : ?>
		    <input type="radio" name="server" value="<?php echo $row['id']; ?>" /> <?php echo $row['servername']; ?><br />
	    <?php endforeach; ?>
	    <br />
	  <input class="btn btn-default" type="submit" value="submit" />
	</form>
	<a href="Servers.php" class="btn btn-lg btn-link" style="float: right;">Back to Servers</a>
	<br /><br />
</div>
<?php
if(isset($_GET['server'])){
	$server = $serverController->getServerByID($_GET['server']);
	$serverController->removeServer($server);
}
	//Include a generic footer
	include 'inc/html/footer.php';
?>
