<?php
/**
 * User: Andy Abbott
 * Date: 21/08/2015
 * Time: 16:08
 */
namespace Synx\Controller;

include_once 'AbstractController.php';

use Synx\Model\Package;

class PackageController extends AbstractController
{
    /**
     * Get an array of the packages
     * @return array
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getPackages(){
        //ToDo: Consider pagination and sorting
        $params = array();
        $sql = "SELECT `id` AS `_id`, `name` AS `_name` FROM `package`";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_CLASS, Package::class);
    }


    /**
     * Get an instance of the package based upon the package ID
     * @param int $id
     * @return Package
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getPackageByID($id){
        //Create new package object to and set id to get Exceptions for incorrect format
        $package = new Package();
        $package->setId($id);

        $params = array('package_id' => $package->getId());
        $sql = "SELECT `id` AS `_id`, `name` AS `_name` FROM `package` WHERE `id` = :package_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception(Package::class.' cannot be found, check the ID is valid');
        }
        return $statement->fetchObject(Package::class);
    }

    /**
     * Get an instance of the package based upon the package Name
     * @param string $name
     * @return Package
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getPackageByName($name){
        //Create new package object to and set name to get Exceptions for incorrect format
        $package = new Package();
        $package->setName($name);

        $params = array('package_name' => $package->getName());
        $sql = "SELECT `id` AS `_id`, `name` AS `_name` FROM `package` WHERE `name` = :package_name";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception(Package::class.' cannot be found, check the Name is valid');
        }
        return $statement->fetchObject(Package::class);
    }

    /**
     * Add Package Info to DB
     * @param Package $package
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function addPackage(Package &$package){
        $params = $package->toArray();
        $supported_keys = array('package_name');

        self::filterParams($params,$supported_keys);

        $sql = "INSERT INTO `package` (`name`)
                VALUES (:package_name)";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $package->setId($this->getDbConnection()->lastInsertId());
    }

    /**
     * Update Package Info to DB
     * @param Package $package
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function updatePackage(Package &$package){
        $params = $package->toArray();
        $supported_keys = array('package_id', 'package_name');

        self::filterParams($params,$supported_keys);

        $sql = "UPDATE `package`
                SET `name` = :package_name
                WHERE `id` = :package_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }

    /**
     * Remove Package from DB
     * @param Package $package
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function removePackage(Package $package){
        $params = $package->toArray();
        $supported_keys = array('package_id');

        self::filterParams($params,$supported_keys);

        $sql = "DELETE FROM `package_update` INNER JOIN `package_version` ON `package_update`.`package_version_id` = `package_version`.`id` INNER JOIN `package` ON `package_version`.`package_id` = `package`.`id` WHERE `package`.`id` = :package_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE FROM `package_version` INNER JOIN `package` ON `package_version`.`package_id` = `package`.`id` WHERE `package`.`id` = :package_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE FROM `package` WHERE `id` = :package_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }
}