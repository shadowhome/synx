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
        $sql = "SELECT `id` AS `_id`, `servername` AS `_name`, `ip` AS `_ip`, `company` AS `_company`, `OS` AS `_osName`, `version` AS `_osVersionCode`, `description` AS `_description`, `releasever` AS `_osVersionName` FROM `servers`";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_CLASS, Package::class);
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
        $supported_keys = array('package_name', 'is_upgrade', 'is_security', 'changelog', 'server_id', 'current_version_code', 'new_version_code', 'install_date', 'md5', 'is_installed', 'is_removed');
        self::filterParams($params,$supported_keys);


        $sql = "INSERT INTO `packages` (`package`, `security`, `upgrade`,`servers`, `changelog`, `date`, `md5`, `version`, `nversion`, `ii`, `rc`)
                VALUES (:package_name, :is_security, :is_upgrade, :server_id, :changelog, :install_date, :md5, :current_version_code, :new_version_code, :is_installed, :is_removed";
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
        $supported_keys = array('package_id', 'package_name', 'is_upgrade', 'is_security', 'changelog', 'server_id', 'current_version_code', 'new_version_code', 'install_date', 'md5', 'is_installed', 'is_removed');

        self::filterParams($params,$supported_keys);

        $sql = "UPDATE `packages`
                SET `package` = :package_name,
                    `security` = :is_security,
                    `upgrade` = :upgrade,
                    `servers` = :server_id,
                    `changelog` = :changelog,
                    `date` = :install_date,
                    `md5` = :md5,
                    `version` = :current_version_code,
                    `nversion` = :new_version_code,
                    `li` = :is_installed,
                    `rc` = :is_removed
                WHERE `id` = :package_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }

    /**
     * Remove Package From DB
     * @param Package $package
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function removePackage(Package $package){
        $params = $package->toArray();
        $supported_keys = array('package_id');

        self::filterParams($params,$supported_keys);

        $sql = "DELETE FROM `packages` WHERE `packages` = :package_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }
}