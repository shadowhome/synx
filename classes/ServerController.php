<?php
/**
 * User: Andy Abbott
 * Date: 21/08/2015
 * Time: 09:33
 */
namespace Synx\Controller;

include_once 'AbstractController.php';

use Synx\Model\Server;
use PDO;
use InvalidArgumentException;
use PDOException;
use Exception;

class ServerController extends AbstractController
{
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
        $sql = "SELECT `id` AS `_id`, `servername` AS `_name`, `ip` AS `_ip`, `company` AS `_company`, `OS` AS `_osName`, `version` AS `_osVersionCode`, `description` AS `_description`, `releasever` AS `_osVersionName` FROM `servers`";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_CLASS, 'Synx\Model\Server');
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
        if(!is_int($id)){
            throw new InvalidArgumentException('Get Server: ID is invalid');
        }
        $params = array('server_id' => $id);
        $sql = "SELECT `id` AS `_id`, `servername` AS `_name`, `ip` AS `_ip`, `company` AS `_company`, `OS` AS `_osName`, `version` AS `_osVersionCode`, `description` AS `_description`, `releasever` AS `_osVersionName` FROM `servers` WHERE `id` = :server_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception('Server cannot be found, check the ID is valid');
        }
        return $statement->fetchObject('Synx\Model\Server');
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
        if(!is_string($ip)){
            throw new InvalidArgumentException('Get Server: IP is invalid');
        }
        if(!filter_var($ip,FILTER_VALIDATE_IP)){
            throw new InvalidArgumentException('Get Server: IP is not valid');
        }
        $params = array('server_ip' => $ip);
        $sql = "SELECT `id` AS `_id`, `servername` AS `_name`, `ip` AS `_ip`, `company` AS `_company`, `OS` AS `_osName`, `version` AS `_osVersionCode`, `description` AS `_description`, `releasever` AS `_osVersionName` FROM `servers` WHERE `ip` = :server_ip";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception('Server cannot be found, check the IP is valid');
        }
        return $statement->fetchObject('Synx\Model\Server');
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
        $supported_keys = array('server_name', 'ip', 'company', 'os_name', 'os_version_code', 'description', 'os_version_name');

        self::filterParams($params,$supported_keys);

        $sql = "INSERT INTO `servers` (`servername`, `ip`, `company`,`OS`, `version`, `description`, `releasever`)
                VALUES (:server_name, :ip, :company, :os_name, :os_version_code, :description, :os_version_name";
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
        $supported_keys = array('server_id', 'server_name', 'ip', 'company', 'os_name', 'os_version_code', 'description', 'os_version_name');

        self::filterParams($params,$supported_keys);

        $sql = "UPDATE `servers`
                SET `servername` = :server_name,
                    `ip` = :ip,
                    `company` = :company,
                    `OS` = :os_name,
                    `version` = :os_version_code,
                    `description` = :description,
                    `releasever` = :os_version_name
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

        $sql = "DELETE FROM `packagesHist` WHERE `servers` = :server_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE FROM `packages` WHERE `servers` = :server_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE FROM `servers` WHERE `id` = :server_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }
}