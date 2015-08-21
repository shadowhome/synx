<?php
include __DIR__.'/inc/functions.php';

//ToDo: Change Classes to autoload
include __DIR__.'/classes/Server.php';
include __DIR__.'/classes/ServerController.php';

use Synx\Controller\ServerController;

$serverController = new ServerController();
$servers = $serverController->getServers();
?>
<form action="DelServer.php" method="get">
    <?php foreach($servers as $server) : ?>

    <input type="radio" name="server"  value="<?php echo $server->getId(); ?>" /> <?php echo $server->getName(); ?>
    
    <?php endforeach; ?>

  <input type="submit" value="submit" />

</form>
<?php
if(isset($_GET['server'])){
	$server = $serverController->getServerByID($_GET['server']);
	$serverController->removeServer($server);
}

