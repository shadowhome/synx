<?php
/**
 * User: Andy Abbott
 * Date: 21/08/2015
 * Time: 09:33
 */
namespace Synx\Controller;

use Synx\Exception\EmptyResultException;
use Synx\Model\OperatingSystem;
use Synx\Model\Server;
use PDO;
use InvalidArgumentException;
use PDOException;
use Exception;
use Synx\Resource\ServerConnection;

class ServerController extends AbstractController
{
    use ServerConnection;

    /**
     * Get an array of the servers
     * @return array
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getServers(){
        //ToDo: Consider pagination and sorting
        $params = array();
        $sql = "SELECT
                    `id` AS `_id`,
                    `name` AS `_name`,
                    `ip` AS `_ip`,
                    `port` AS `_port`,
                    `description` AS `_description`,
                    `company_id` AS `_companyId`,
                    `os_version_id` AS `_osVersionId`,
                    `ram` AS `_ram`,
                    `cpu_frequency` AS `_cpuFrequency`,
                    `cpu_architecture` AS `_cpuArchitecture`,
                    `cpu_number` AS `_cpuNumber`,
                    `cpu_sockets` AS `_cpuSockets`,
                    `cpu_threads` AS `_cpuThreads`,
                    `cpu_core` AS `_cpuCore`
                FROM `server`";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_CLASS, Server::class);
    }

    /**
     * Get an instance of the server based upon the server ID
     * @param int $id
     * @return Server
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getServerByID($id){
        //Create new server object to and set id to get Exceptions for incorrect format
        $server = new Server();
        $server->setId($id);

        $params = array('server_id' => $id);
        $sql = "SELECT
                    `id` AS `_id`,
                    `name` AS `_name`,
                    `ip` AS `_ip`,
                    `port` AS `_port`,
                    `description` AS `_description`,
                    `company_id` AS `_companyId`,
                    `os_version_id` AS `_osVersionId`,
                    `ram` AS `_ram`,
                    `cpu_frequency` AS `_cpuFrequency`,
                    `cpu_architecture` AS `_cpuArchitecture`,
                    `cpu_number` AS `_cpuNumber`,
                    `cpu_sockets` AS `_cpuSockets`,
                    `cpu_threads` AS `_cpuThreads`,
                    `cpu_core` AS `_cpuCore`
                FROM `server`
                WHERE `id` = :server_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception('Server cannot be found, check the ID is valid');
        }
        return $statement->fetchObject(Server::class);
    }

    /**
     * Get an instance of the server based upon the server IP Address
     * @param string $ip
     * @return Server
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getServerByIP($ip){
        //Create new server object to and set id to get Exceptions for incorrect format
        $server = new Server();
        $server->setIp($ip);

        $params = array('server_ip' => $ip);
        $sql = "SELECT
                    `id` AS `_id`,
                    `name` AS `_name`,
                    `ip` AS `_ip`,
                    `port` AS `_port`,
                    `description` AS `_description`,
                    `company_id` AS `_companyId`,
                    `os_version_id` AS `_osVersionId`,
                    `ram` AS `_ram`,
                    `cpu_frequency` AS `_cpuFrequency`,
                    `cpu_architecture` AS `_cpuArchitecture`,
                    `cpu_number` AS `_cpuNumber`,
                    `cpu_sockets` AS `_cpuSockets`,
                    `cpu_threads` AS `_cpuThreads`,
                    `cpu_core` AS `_cpuCore`
                FROM `server`
                WHERE `ip` = :server_ip";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception('Server cannot be found, check the IP is valid');
        }
        return $statement->fetchObject(Server::class);
    }

    /**
     * Add Server Info to DB
     * @param Server $server
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function addServer(Server &$server){
        $params = $server->toArray();
        $supported_keys = array('server_name', 'ip', 'port', 'description', 'company_id', 'os_version_id');

        self::filterParams($params,$supported_keys);

        $sql = "INSERT INTO `server` (`name`, `ip`, `port`, `description`, `company_id`,`os_version_id`, `ram`, `cpu_frequency, `cpu_architecture`, `cpu_number`, `cpu_sockets`, `cpu_threads`, `cpu_core`)
                VALUES (:server_name, :ip, :port, :description, :company_id, :os_version_id, :ram, :cpu_frequency, :cpu_architecture, :cpu_number, :cpu_sockets, :cpu_threads, :cpu_core)";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $server->setId($this->getDbConnection()->lastInsertId());
    }

    /**
     * Update Server Info to DB
     * @param Server $server
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function updateServer(Server &$server){
        $params = $server->toArray();
        $supported_keys = array('server_id', 'server_name', 'ip', 'port', 'description', 'company_id', 'os_version_id');

        self::filterParams($params,$supported_keys);

        $sql = "UPDATE `server`
                SET `name` = :server_name,
                    `ip` = :ip,
                    `port` = :port,
                    `description` = :description,
                    `company_id` = :company_id,
                    `os_version_id` = :os_version_id,
                    `ram` AS `:ram`,
                    `cpu_frequency` AS `:cpu_frequency`,
                    `cpu_architecture` AS `:cpu_architecture`,
                    `cpu_number` AS `:cpu_number`,
                    `cpu_sockets` AS `:cpu_sockets`,
                    `cpu_threads` AS `:cpu_threads`,
                    `cpu_core` AS `:cpu_core`
                WHERE `id` = :server_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }

    /**
     * Remove Server From DB
     * @param Server $server
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function removeServer(Server $server){
        $params = $server->toArray();
        $supported_keys = array('server_id');

        self::filterParams($params,$supported_keys);

        $sql = "DELETE `package_update`.* FROM `package_update` INNER JOIN `server` ON `package_update`.`server_id` = `server`.`id` WHERE `server`.`id` = :server_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE `server`.* FROM `server` WHERE `id` = :server_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }

    public function checkOperatingSystem(Server $server){
        $cmd="lsb_release -as";

        $response = $this->runCommand($server, $cmd);

        $response = explode("\n", $response);
        if(empty($response)) {
            throw new EmptyResultException('No Operating System Info was returned for server ['.$server->getIp().']');
        }

        $operatingSystemController = new OperatingSystemController();
        $operatingSystem = null;
        try {
            $operatingSystem = $operatingSystemController->getOperatingSystemByName($response[0]);
        } catch (EmptyResultException $e){
            $operatingSystem = new OperatingSystem();
            $operatingSystem->setName($response[0]);
            $operatingSystemController->addOperatingSystem($operatingSystem);
        }

        $operatingSystemVersionController = new OperatingSystemVersionController();
        $operatingSystemVersion = null;
        try {
            $operatingSystemVersion = $operatingSystemVersionController->getOperatingSystemVersionByCode($response[2]);
        } catch (EmptyResultException $e){
            $operatingSystemVersion = new OperatingSystemVersion();
            $operatingSystemVersion
                ->setCode($response[2])
                ->setName($response[3])
                ->setOsId($operatingSystem->getId());
            $operatingSystemVersionController->addOperatingSystemVersion($operatingSystemVersion);
        }

        $server->setOsVersionId($operatingSystemVersion->getOsVersionId());

        //server not updated as it may need to be either added or updated..
    }
}