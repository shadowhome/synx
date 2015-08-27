<?php
/**
 * User: Andy Abbott
 * Date: 21/08/2015
 * Time: 15:02
 */
namespace Synx\Controller;

use Synx\Model\OperatingSystem;
use PDO;
use InvalidArgumentException;
use PDOException;
use Exception;

class OperatingSystemController extends AbstractController
{
    /**
     * Get an array of the Operating Systems
     * @return array
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getOperatingSystems(){
        //ToDo: Consider pagination and sorting
        $params = array();
        $sql = "SELECT `id` AS `_id`, `name` AS `_name` FROM `operating_system`";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_CLASS, OperatingSystem::class);
    }

    /**
     * Get an instance of the operating system based upon the operating system ID
     * @param int $id
     * @return OperatingSystem
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getOperatingSystemByID($id){
        //Create new operatingSystem object to and set id to get Exceptions for incorrect format
        $operatingSystem = new OperatingSystem();
        $operatingSystem->setId($id);

        $params = array('os_id' => $operatingSystem->getId());
        $sql = "SELECT `id` AS `_id`, `name` AS `_name` FROM `operating_system` WHERE `id` = :os_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception(OperatingSystem::class.' cannot be found, check the ID is valid');
        }
        return $statement->fetchObject(OperatingSystem::class);
    }

    /**
     * Get an instance of the operating system based upon the operating system Name
     * @param string $name
     * @return OperatingSystem
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getOperatingSystemByName($name){
        //Create new operatingSystem object to and set name to get Exceptions for incorrect format
        $operatingSystem = new OperatingSystem();
        $operatingSystem->setName($name);

        $params = array('os_name' => $operatingSystem->getName());
        $sql = "SELECT `id` AS `_id`, `name` AS `_name` FROM `operating_system` WHERE `name` = :os_name";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception(OperatingSystem::class.' cannot be found, check the Name is valid');
        }
        return $statement->fetchObject(OperatingSystem::class);
    }

    /**
     * Add Operating System Info to DB
     * @param OperatingSystem $os
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function addOperatingSystem(OperatingSystem &$os){
        $params = $os->toArray();
        $supported_keys = array('os_name');

        self::filterParams($params,$supported_keys);

        $sql = "INSERT INTO `operating_system` (`name`)
                VALUES (:os_name)";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $os->setId($this->getDbConnection()->lastInsertId());
    }

    /**
     * Update Operating System Info to DB
     * @param OperatingSystem $os
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function updateOperatingSystem(OperatingSystem &$os){
        $params = $os->toArray();
        $supported_keys = array('os_id', 'os_name');

        self::filterParams($params,$supported_keys);

        $sql = "UPDATE `operating_system`
                SET `name` = :os_name
                WHERE `id` = :os_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }

    /**
     * Remove Operating System from DB
     * @param OperatingSystem $os
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function removeOperatingSystem(OperatingSystem $os){
        $params = $os->toArray();
        $supported_keys = array('os_id');

        self::filterParams($params,$supported_keys);

        $sql = "DELETE `package_update`.* FROM `package_update` INNER JOIN `server` ON `package_update`.`server_id` = `server`.`id` INNER JOIN `operating_system_version` ON `server`.`os_version_id` = `operating_system_version`.`id` INNER JOIN `operating_system` ON `operating_system_version`.`os_id` = `operating_system`.`id` WHERE `operating_system`.`id` = :os_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE `server`.* FROM `server` INNER JOIN `operating_system_version` ON `server`.`os_version_id` = `operating_system_version`.`id` INNER JOIN `operating_system` ON `operating_system_version`.`os_id` = `operating_system`.`id` WHERE `operating_system`.`id` = :os_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE `operating_system_version`.* FROM `operating_system_version` INNER JOIN `operating_system` ON `operating_system_version`.`os_id` = `operating_system`.`id` WHERE `operating_system`.`id` = :os_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE `operating_system`.* FROM `operating_system` WHERE `id` = :os_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }
}