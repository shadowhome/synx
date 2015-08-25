<?php
/**
 * User: Andy Abbott
 * Date: 21/08/2015
 * Time: 15:02
 */
namespace Synx\Controller;

include_once 'AbstractController.php';

use Synx\Model\Company;
use PDO;
use InvalidArgumentException;
use PDOException;
use Exception;

class CompanyController extends AbstractController
{
    /**
     * Get an array of the companies
     * @return array
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getCompanies(){
        //ToDo: Consider pagination and sorting
        $params = array();
        $sql = "SELECT `id` AS `_id`, `name` AS `_name` FROM `company`";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_CLASS, Company::class);
    }

    /**
     * Get an instance of the company based upon the company ID
     * @param int $id
     * @return Company
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getCompanyByID($id){
        //Create new company object to and set id to get Exceptions for incorrect format
        $company = new Company();
        $company->setId($id);

        $params = array('company_id' => $company->getId());
        $sql = "SELECT `id` AS `_id`, `name` AS `_name` FROM `company` WHERE `id` = :company_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception(Company::class.' cannot be found, check the ID is valid');
        }
        return $statement->fetchObject(Company::class);
    }

    /**
     * Get an instance of the company based upon the company Name
     * @param string $name
     * @return Company
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getCompanyByName($name){
        //Create new company object to and set name to get Exceptions for incorrect format
        $company = new Company();
        $company->setName($name);

        $params = array('company_name' => $company->getName());
        $sql = "SELECT `id` AS `_id`, `name` AS `_name` FROM `company` WHERE `name` = :company_name";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
        if($statement->rowCount() !== 1){
            throw new Exception(Company::class.' cannot be found, check the Name is valid');
        }
        return $statement->fetchObject(Company::class);
    }

    /**
     * Add Company Info to DB
     * @param Company $company
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function addCompany(Company &$company){
        $params = $company->toArray();
        $supported_keys = array('company_name');

        self::filterParams($params,$supported_keys);

        $sql = "INSERT INTO `company` (`name`)
                VALUES (:company_name)";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $company->setId($this->getDbConnection()->lastInsertId());
    }

    /**
     * Update Company Info to DB
     * @param Company $company
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function updateCompany(Company &$company){
        $params = $company->toArray();
        $supported_keys = array('company_id', 'company_name');

        self::filterParams($params,$supported_keys);

        $sql = "UPDATE `company`
                SET `name` = :company_name
                WHERE `id` = :company_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }

    /**
     * Remove Company from DB
     * @param Company $company
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    public function removeCompany(Company $company){
        $params = $company->toArray();
        $supported_keys = array('company_id');

        self::filterParams($params,$supported_keys);

        $sql = "DELETE FROM `package_update` INNER JOIN `server` ON `package_update`.`server_id` = `server`.`id` INNER JOIN `company` ON `server`.`company_id` = `company`.`id` WHERE `company`.`id` = :company_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE FROM `server` INNER JOIN `company` ON `server`.`company_id` = `company`.`id` WHERE `company`.`id` = :company_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);

        $sql = "DELETE FROM `company` WHERE `id` = :company_id";
        $statement = $this->getDbConnection()->prepare($sql);
        $statement->execute($params);
    }
}