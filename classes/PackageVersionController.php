<?php
/**
 * User: Andy Abbott
 * Date: 25/08/2015
 * Time: 10:08
 */
namespace Synx\Controller;

include_once 'AbstractController.php';

use Synx\Model\Package;

class PackageVersionController extends AbstractController
{
    /**
     * Get an array of the Package Versions
     * @return array
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getPackageVersions(){
        //ToDo: Consider pagination and sorting
        $params = array();
        $sql = "SELECT `id` AS `_id`, `code` AS `_code`, `security` AS `_security`, `package_id` AS `_packageId`, `changelog` AS `_changelog`, `md5` AS `_md5` FROM `package_version`";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_CLASS, PackageVersion::class);
    }

    /**
     * Get an instance of the package version based upon the package version ID
     * @param int $id
     * @return PackageVersion
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getPackageVersionByID($id){
        //Create new packageVersion object to and set id to get Exceptions for incorrect format
        $packageVersion = new PackageVersion();
        $packageVersion->setId($id);

        $params = array('package_version_id' => $packageVersion->getId());
        $sql = "SELECT `id` AS `_id`, `code` AS `_code`, `security` AS `_security`, `package_id` AS `_packageId`, `changelog` AS `_changelog`, `md5` AS `_md5` FROM `package_version` WHERE `id` = :package_version_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception(PackageVersion::class.' cannot be found, check the ID is valid');
        }
        return $statement->fetchObject(PackageVersion::class);
    }

    /**
     * Get an instance of the package version based upon the package and version code
     * @param Package $package
     * @param string $code
     * @return PackageVersion
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getPackageVersionByCode(Package $package, $code){
        //Create new package object to and set name to get Exceptions for incorrect format
        $packageVersion = new PackageVersion();
        $packageVersion->setPackageId($package->getId())->setCode($code);

        $params = $packageVersion->toArray();
        $supported_keys = array('package_id', 'package_version_code');
        self::filterParams($params, $supported_keys);
        $sql = "SELECT `id` AS `_id`, `code` AS `_code`, `security` AS `_security`, `package_id` AS `_packageId`, `changelog` AS `_changelog`, `md5` AS `_md5` FROM `package_version` WHERE `package_id` = :package_id AND `code` = :package_version_code";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception(PackageVersion::class.' cannot be found, check the Code is valid');
        }
        return $statement->fetchObject(PackageVersion::class);
    }

    /**
     * Add Package Version Info to DB
     * @param PackageVersion $package_version
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function addPackageVersion(PackageVersion &$package_version){
        $params = $package_version->toArray();
        $supported_keys = array('package_version_code', 'security', 'changelog', 'package_id', 'md5');

        self::filterParams($params,$supported_keys);

        $sql = "INSERT INTO `package_version` (`code`, `security`, `package_id`, `changelog`, `md5`)
                VALUES (:package_version_code, :security, :package_id, :changelog, :md5)";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $package_version->setId($this->getDbConnection()->lastInsertId());
    }

    /**
     * Update Package Version Info to DB
     * @param PackageVersion $package_version
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function updatePackageVersion(PackageVersion &$package_version){
        $params = $package_version->toArray();
        $supported_keys = array('package_version_id', 'package_version_code', 'security', 'changelog', 'package_id', 'md5');

        self::filterParams($params,$supported_keys);

        $sql = "UPDATE `package_version`
                SET `code` = :package_version_code,
                    `security` = :security,
                    `package_id` = :package_id,
                    `changelog` = :changelog,
                    `md5` = :md5
                WHERE `id` = :package_version_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }

    /**
     * Remove Package Version from DB
     * @param PackageVersion $package_version
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function removePackageVersion(PackageVersion $package_version){
        $params = $package_version->toArray();
        $supported_keys = array('package_version_id');

        self::filterParams($params,$supported_keys);

        $sql = "DELETE FROM `package_update` INNER JOIN `package_version` ON `package_update`.`package_version_id` = `package_version`.`id` WHERE `package_version`.`id` = :package_version_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE FROM `package_version` WHERE `package_version`.`id` = :package_version_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }
}