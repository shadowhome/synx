<?php
/**
 * User: Andy Abbott
 * Date: 20/08/2015
 * Time: 12:05
 */
namespace Synx\Model;

include_once 'AbstractModel.php';

use InvalidArgumentException;
use Exception;

class PackageUpdate extends AbstractModel
{
    const UPDATE_STATE_NEW = 'NEW';
    const UPDATE_STATE_INSTALLED = 'INSTALLED';
    const UPDATE_STATE_REMOVED = 'REMOVED';

    private $_id;
    private $_packageVersionId;
    private $_serverId;
    private $_newDate;
    private $_installedDate;
    private $_removedDate;

    /**
     * Get the unique ID for the update
     * @return int
     * @throws Exception
     */
    public function getId(){
        if(!$this->_id){
            throw new Exception('Update ID has not been set.');
        }
        return $this->_id;
    }

    /**
     * Set the unique ID for the update
     * @param int $id
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setId($id){
        self::_validateRequiredInt($id);
        $this->_id = $id;
        return $this;
    }

    /**
     * Get the associated Package Version Id
     * @return int
     * @throws Exception
     *
     */
    public function getPackageVersionId()
    {
        if(!$this->_packageVersionId){
            throw new Exception('Update Package Version ID has not been set.');
        }
        return $this->_packageVersionId;
    }

    /**
     * Set the associated Package Version Id
     * @param int $packageVersionId
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setPackageVersionId($packageVersionId)
    {
        self::_validateRequiredInt($packageVersionId);
        $this->_packageVersionId = $packageVersionId;
        return $this;
    }


    /**
     * Get the associated Server Id
     * @return int
     * @throws Exception
     *
     */
    public function getServerId()
    {
        if(!$this->_serverId){
            throw new Exception('Update Server ID has not been set.');
        }
        return $this->_serverId;
    }

    /**
     * Set the associated Server Id
     * @param int $serverId
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setServerId($serverId)
    {
        self::_validateRequiredInt($serverId);
        $this->_serverId = $serverId;
        return $this;
    }

    public function getState(){
        $state = self::UPDATE_STATE_NEW;
        if(isset($this->_removedDate) && $this->_removedDate){
            $state = self::UPDATE_STATE_REMOVED;
        }elseif(isset($this->_installedDate) && $this->_installedDate){
            $state = self::UPDATE_STATE_INSTALLED;
        }
        return $state;
    }

    /**
     * Returns whether the Update is new and has not yet been processed
     * @return bool
     */
    public function isNew(){
        return $this->getState() === self::UPDATE_STATE_NEW;
    }

    /**
     * Returns whether the Update is currently installed
     * @return bool
     */
    public function isInstalled(){
        return $this->getState() === self::UPDATE_STATE_INSTALLED;
    }

    /**
     * Returns whether the Update is currently removed
     * @return bool
     */
    public function isRemoved(){
        return $this->getState() === self::UPDATE_STATE_REMOVED;
    }

    /**
     * Returns the date when the update was discovered
     * @return string | null
     */
    public function getNewDate(){
        return $this->_newDate;
    }

    /**
     * Set the date when the update was discovered, if $date is false, installedDate and removedDate are reset
     * @param bool|false $date
     */
    public function setNewDate($date = false){
        if(!$date){
            $date = date('Y-m-d');
            $this->_installedDate = null;
            $this->_removedDate = null;
        }
        self::_validateRequiredDate($date);
        $this->_installedDate($date);
    }

    /**
     * Returns the date when the update was installed
     * @return string, null
     */
    public function getInstalledDate(){
        return $this->_installedDate;
    }

    /**
     * Set the date when the update was installed, if $date is false, removedDate is reset
     * @param bool|false $date
     */
    public function setInstalledDate($date = false){
        if(!$date){
            $date = date('Y-m-d');
            $this->_removedDate = null;
        }
        self::_validateRequiredDate($date);
        $this->_installedDate($date);
    }

    /**
     * Returns the date when the update was installed
     * @return string, null
     */
    public function getRemovedDate(){
        return $this->_removedDate;
    }

    /**
     * Set the date when the update was removed
     * @param bool|false $date
     */
    public function setRemovedDate($date = false){
        if(!$date){
            $date = date('Y-m-d');
        }
        self::_validateRequiredDate($date);
        $this->_removedDate($date);
    }

    /**
     * Return Simple Format for displaying update info
     * @return string
     */
    public function toString(){
        return 'Server '.$this->_id.': '.$this->_serverId.' ['.$this->getState().']';
    }

    /**
     * Return object as formatted array
     * @return array
     */
    public function toArray(){
        $result = array();
        $result['package_update_id'] = $this->_id;
        $result['server_id'] = $this->_serverId;
        $result['package_version_id'] = $this->_packageVersionId;
        $result['new_date'] = $this->_new_date;
        $result['installed_date'] = $this->_installed_date;
        $result['removed_date'] = $this->_removed_date;
        return $result;
    }
}