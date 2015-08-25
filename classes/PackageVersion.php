<?php
/**
 * User: Andy Abbott
 * Date: 24/08/2015
 * Time: 12:01
 */
namespace Synx\Model;

include_once 'AbstractModel.php';

use InvalidArgumentException;
use Exception;

class PackageVersion extends AbstractModel
{
    private $_id;
    private $_code;
    private $_security = false;
    private $_packageId;
    private $_changelog = '';
    private $_md5;

    /**
     * Get the unique identifier for the package version
     * @return int
     * @throws Exception
     */
    public function getId()
    {
        if(!$this->_id){
            throw new Exception('Package ID has not been set.');
        }
        return $this->_id;
    }

    /**
     * Set the unique identifier for the package version
     * @param int $id
     * @return Package
     * @throws InvalidArgumentException
     */
    public function setId($id)
    {
        self::_validateRequiredInt($id);
        $this->_id = $id;
        return $this;
    }

    /**
     * Get the unique version code for the package version
     * @return string
     * @throws Exception
     */
    public function getCode()
    {
        if(!$this->_code){
            throw new Exception('Package Version Code has not been set.');
        }
        return $this->_code;
    }

    /**
     * Set the unique code for the package version
     * @param string $code
     * @return Package
     * @throws InvalidArgumentException
     */
    public function setCode($code)
    {
        self::_validateRequiredString($code);
        $this->_code = $code;
        return $this;
    }

    /**
     * Returns whether the package version is security related (more urgent) or not
     * @return boolean
     */
    public function isSecurity()
    {
        return $this->_security;
    }

    /**
     * Sets whether the package version is security related (more urgent) or not
     * @param boolean $security
     * @return Package
     */
    public function setSecurity($security)
    {
        $this->_security = (bool)$security;
        return $this;
    }

    /**
     * Get the ID of the associated package
     * @return int
     */
    public function getPackageId()
    {
        return $this->_packageId;
    }

    /**
     * Set the ID of the associated package
     * @param int $packageId
     * @return Package
     * @throws InvalidArgumentException
     */
    public function setPackageId($packageId)
    {
        self::_validateRequiredInt($packageId);
        $this->_packageId = $packageId;
        return $this;
    }

    /**
     * Get the changelog
     * @return string
     */
    public function getChangelog()
    {
        return $this->_changelog;
    }

    /**
     * Set the changelog
     * @param string $changelog
     * @return Package
     * @throws InvalidArgumentException
     */
    public function setChangelog($changelog)
    {

        self::_validateRequiredString($changelog, true);
        $this->_changelog = $changelog;
        return $this;
    }


    /**
     * Return the MD5 key for the package version
     * @return string
     */
    public function getMd5()
    {
        return $this->_md5;
    }

    /**
     * Set teh MD5 key for the package version
     * @param string $md5
     * @return Package
     * @throws InvalidArgumentException
     */
    public function setMd5($md5)
    {
        self::_validateRequiredString($md5);
        $this->_md5 = $md5;
        return $this;
    }

    function toString()
    {
        return 'Package '.$this->_id.': '.$this->_name.' ['.$this->_serverId.']';
    }

    function toArray()
    {
        $result = array();
        $result['package_version_id'] = $this->_id;
        $result['package_version_code'] = $this->_versionCode;
        $result['security'] = $this->_security;
        $result['changelog'] = $this->_changelog;
        $result['package_id'] = $this->_packageId;
        $result['md5'] = $this->_md5;
        return $result;
    }
}