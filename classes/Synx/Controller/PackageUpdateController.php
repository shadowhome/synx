<?php
/**
 * User: Andy Abbott
 * Date: 25/08/2015
 * Time: 11:29
 */
namespace Synx\Controller;

use Synx\Model\PackageUpdate;

class PackageUpdateController extends AbstractController
{
    /**
     * Get an array of the Package Updates
     * @return array
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getPackageUpdates(){
        //ToDo: Consider pagination and sorting
        $params = array();
        $sql = "SELECT `id` AS `_id`, `package_version_id` AS `_packageVersionId`, `server_id` AS `_serverId`, `new_date` AS `_newDate`, `installed_date` AS `_installedDate`, `removed_date` AS `_removedDate` FROM `package_update`";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_CLASS, PackageUpdate::class);
    }

    /**
     * Get an instance of the package update based upon the package update ID
     * @param int $id
     * @return PackageUpdate
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getPackageUpdateByID($id){
        //Create new packageUpdate object to and set id to get Exceptions for incorrect format
        $packageUpdate = new PackageUpdate();
        $packageUpdate->setId($id);

        $params = array('package_update_id' => $packageUpdate->getId());
        $sql = "SELECT  `id` AS `_id`, `package_version_id` AS `_packageVersionId`, `server_id` AS `_serverId`, `new_date` AS `_newDate`, `installed_date` AS `_installedDate`, `removed_date` AS `_removedDate` FROM `package_update` WHERE `id` = :package_update_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception(PackageUpdate::class.' cannot be found, check the ID is valid');
        }
        return $statement->fetchObject(PackageUpdate::class);
    }

    /**
     * Add Package Update Info to DB
     * @param PackageUpdate $package_update
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function addPackageUpdate(PackageUpdate &$package_update){
        $params = $package_update->toArray();
        $supported_keys = array('server_id', 'package_version_id', 'new_date', 'installed_date', 'removed_date');

        self::filterParams($params,$supported_keys);

        $sql = "INSERT INTO `package_update` (`package_version_id`, `server_id`, `new_date`, `installed_date`, `removed_date`)
                VALUES (:package_version_id, :server_id, :new_date, :installed_date, :removed_date)";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $package_update->setId($this->getDbConnection()->lastInsertId());
    }

    /**
     * Update Package Update Info to DB
     * @param Packageupdate $package_update
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function updatePackageUpdate(PackageUpdate &$package_update){
        $params = $package_update->toArray();
        $supported_keys = array('package_update_id', 'server_id', 'package_version_id', 'new_date', 'installed_date', 'removed_date');

        self::filterParams($params,$supported_keys);

        $sql = "UPDATE `package_update`
                SET `package_version_id` = :package_version_id,
                    `server_id` = :server_id,
                    `new_date` = :new_date,
                    `installed_date` = :installed_date,
                    `removed_date` = :removed_date
                WHERE `id` = :package_update_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }

    /**
     * Remove Package Update from DB
     * @param PackageUpdate $package_update
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function removePackageUpdate(PackageUpdate $package_update){
        $params = $package_update->toArray();
        $supported_keys = array('package_update_id');

        self::filterParams($params,$supported_keys);

        $sql = "DELETE FROM `package_update` WHERE `package_update`.`id` = :package_update_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }
}