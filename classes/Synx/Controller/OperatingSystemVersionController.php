<?php
/**
 * User: Andy Abbott
 * Date: 21/08/2015
 * Time: 15:54
 */
namespace Synx\Controller;

use Synx\Model\OperatingSystem;
use Synx\Model\OperatingSystemVersion;
use PDO;
use InvalidArgumentException;
use PDOException;
use Exception;

class OperatingSystemVersionController extends AbstractController
{
    /**
     * Get an array of the Operating System Versions
     * @return array
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getOperatingSystemVersions(){
        //ToDo: Consider pagination and sorting
        $params = array();
        $sql = "SELECT `id` AS `_id`, `name` AS `_name`, `code` AS `_code`, `os_id` as `_osId` FROM `operating_system_version`";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_CLASS, OperatingSystemVersion::class);
    }

    /**
     * Get an instance of the operating system version based upon the operating system version ID
     * @param int $id
     * @return OperatingSystemVersion
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getOperatingSystemVersionByID($id){
        //Create new operatingSystemVersion object to and set id to get Exceptions for incorrect format
        $operatingSystemVersion = new OperatingSystemVersion();
        $operatingSystemVersion->setId($id);

        $params = array('os_version_id' => $operatingSystemVersion->getId());
        $sql = "SELECT `id` AS `_id`, `name` AS `_name`, `code` AS `_code`, `os_id` AS `_osId` FROM `operating_system_version` WHERE `id` = :os_version_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception(OperatingSystemVersion::class.' cannot be found, check the ID is valid');
        }
        return $statement->fetchObject(OperatingSystemVersion::class);
    }

    /**
     * Get an instance of the operating system version based upon the operating system and version code
     * @param OperatingSystem $os
     * @param string $code
     * @return OperatingSystemVersion
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getOperatingSystemVersionByCode(OperatingSystem $os, $code){
        //Create new operatingSystem object to and set name to get Exceptions for incorrect format
        $operatingSystemVersion = new OperatingSystemVersion();
        $operatingSystemVersion->setOsId($os->getId())->setCode($code);

        $params = $operatingSystemVersion->toArray();
        $supported_keys = array('os_id', 'os_version_code');
        self::filterParams($params, $supported_keys);
        $sql = "SELECT `id` AS `_id`, `name` AS `_name`, `code` AS `_code`, `os_id` AS `_osId` FROM `operating_system_version` WHERE `os_id` = :os_id AND `code` = :os_version_code";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception(OperatingSystemVersion::class.' cannot be found, check the Code is valid');
        }
        return $statement->fetchObject(OperatingSystemVersion::class);
    }

    /**
     * Add Operating System Version Info to DB
     * @param OperatingSystemVersion $os_version
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function addOperatingSystemVersion(OperatingSystemVersion &$os_version){
        $params = $os_version->toArray();
        $supported_keys = array('os_version_name', 'os_version_code', 'os_id');

        self::filterParams($params,$supported_keys);

        $sql = "INSERT INTO `operating_system_version` (`name`, `code`, `os_id`)
                VALUES (:os_version_name, :os_version_code, :os_id)";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $os_version->setId($this->getDbConnection()->lastInsertId());
    }

    /**
     * Update Operating System Version Info to DB
     * @param OperatingSystemVersion $os_version
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function updateOperatingSystemVersion(OperatingSystemVersion &$os_version){
        $params = $os_version->toArray();
        $supported_keys = array('os_version_id', 'os_version_name', 'os_version_code', 'os_id');

        self::filterParams($params,$supported_keys);

        $sql = "UPDATE `operating_system_version`
                SET `name` = :os_version_name,
                    `code` = :os_version_code,
                    `os_id` = :os_id
                WHERE `id` = :os_version_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }

    /**
     * Remove Operating System Version from DB
     * @param OperatingSystemVersion $os_version
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function removeOperatingSystemVersion(OperatingSystemVersion $os_version){
        $params = $os_version->toArray();
        $supported_keys = array('os_version_id');

        self::filterParams($params,$supported_keys);

        $sql = "DELETE FROM `package_update` INNER JOIN `server` ON `package_update`.`server_id` = `server`.`id` INNER JOIN `operating_system_version` ON `server`.`os_version_id` = `operating_system_version`.`id`  WHERE `operating_system_version`.`id` = :os_version_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE FROM `server` INNER JOIN `operating_system_version` ON `server`.`os_version_id` = `operating_system_version`.`id` WHERE `operating_system_version`.`id` = :os_version_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE FROM `operating_system_version` WHERE `operating_system_version`.`id` = :os_version_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }
}