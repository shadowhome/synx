<?php
/**
 * User: Andy Abbott
 * Date: 21/08/2015
 * Time: 13:46
 */
namespace Synx\Model;

include_once 'AbstractModel.php';

use InvalidArgumentException;
use Exception;

class Package extends AbstractModel
{
    private $_id;
    private $_name;
    private $_currentVersionCode;
    private $_isUpgrade = false;
    private $_isSecurity = false;
    private $_serverId;
    private $_changelog = '';
    private $_installDate;
    private $_md5;
    private $_newVersionCode;
    private $_isInstalled = false;
    private $_isRemoved = false;

    /**
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
     * @param int $id
     * @return Package
     * @throws InvalidArgumentException
     */
    public function setId($id)
    {
        if(!is_int($id)){
            throw new InvalidArgumentException('Package ID is not an integer');
        }
        if(!$id){
            throw new InvalidArgumentException('Package ID is null.');
        }
        $this->_id = $id;
        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getName()
    {
        if(!$this->_name){
            throw new Exception('Package Name has not been set.');
        }
        return $this->_name;
    }

    /**
     * @param string $name
     * @return Package
     */
    public function setName($name)
    {
        if(!is_string($name)){
            throw new InvalidArgumentException('Package Name is not a string');
        }
        $name = trim($name);
        if(!$name){
            throw new InvalidArgumentException('Package Name is null.');
        }
        $this->_name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentVersionCode()
    {
        return $this->_currentVersionCode;
    }

    /**
     * @param string $currentVersionCode
     * @return Package
     */
    public function setCurrentVersionCode($currentVersionCode)
    {
        if(!is_string($currentVersionCode)){
            throw new InvalidArgumentException('Package Current Version Code is not a string');
        }
        $this->_currentVersionCode = $currentVersionCode;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isUpgrade()
    {
        return $this->_isUpgrade;
    }

    /**
     * @param boolean $isUpgrade
     * @return Package
     */
    public function setUpgrade($isUpgrade)
    {
        $this->_isUpgrade = (bool)$isUpgrade;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSecurity()
    {
        return $this->_isSecurity;
    }

    /**
     * @param boolean $isSecurity
     * @return Package
     */
    public function setSecurity($isSecurity)
    {
        $this->_isSecurity = (bool)$isSecurity;
        return $this;
    }

    /**
     * @return int
     */
    public function getServerId()
    {
        return $this->_serverId;
    }

    /**
     * @param int $serverId
     * @return Package
     */
    public function setServerId($serverId)
    {

        if(!is_int($serverId)){
            throw new InvalidArgumentException('Package Server ID is not an integer');
        }
        $this->_serverId = $serverId;
        return $this;
    }

    /**
     * @return string
     */
    public function getChangelog()
    {
        return $this->_changelog;
    }

    /**
     * @param string $changelog
     * @return Package
     */
    public function setChangelog($changelog)
    {

        if(!is_string($changelog)){
            throw new InvalidArgumentException('Package Changelog is not a string');
        }
        $this->_changelog = $changelog;
        return $this;
    }

    /**
     * @return date
     */
    public function getInstallDate()
    {
        return $this->_installDate;
    }

    /**
     * @return string
     */
    public function getMd5()
    {
        return $this->_md5;
    }

    /**
     * @param string $md5
     * @return Package
     */
    public function setMd5($md5)
    {
        if(!is_string($md5)){
            throw new InvalidArgumentException('Package MD5 is not a string');
        }
        $this->_md5 = $md5;
        return $this;
    }

    /**
     * @return string
     */
    public function getNewVersionCode()
    {
        return $this->_newVersionCode;
    }

    /**
     * @param string $newVersionCode
     * @return Package
     */
    public function setNewVersionCode($newVersionCode)
    {
        if(!is_string($newVersionCode)){
            throw new InvalidArgumentException('Package New Version Code is not a string');
        }
        $this->_newVersionCode = $newVersionCode;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isInstalled()
    {
        return $this->_isInstalled;
    }

    /**
     * @param boolean $isInstalled
     * @return Package
     */
    public function setIsInstalled($isInstalled)
    {
        $this->_isInstalled = (bool)$isInstalled;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRemoved()
    {
        return $this->_isRemoved;
    }

    /**
     * @param boolean $isRemoved
     * @return Package
     */
    public function setRemoved($isRemoved)
    {
        $this->_isRemoved = (bool)$isRemoved;
        return $this;
    }


    function toString()
    {
        return 'Package '.$this->_id.': '.$this->_name.' ['.$this->_serverId.']';
    }

    function toArray()
    {
        $result = array();
        $result['package_id'] = $this->_id;
        $result['package_name'] = $this->_name;
        $result['is_upgrade'] = $this->_isUpgrade;
        $result['is_security'] = $this->_isSecurity;
        $result['changelog'] = $this->_changelog;
        $result['server_id'] = $this->_serverId;
        $result['current_version_code'] = $this->_currentVersionCode;
        $result['new_version_code'] = $this->_newVersionCode;
        $result['install_date'] = $this->_installDate;
        $result['md5'] = $this->_md5;
        $result['is_installed'] = $this->_isInstalled;
        $result['is_removed'] = $this->_isRemoved;
        return $result;
    }
}