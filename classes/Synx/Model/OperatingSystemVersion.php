<?php
/**
 * User: Andy Abbott
 * Date: 24/08/2015
 * Time: 08:57
 */

namespace Synx\Model;

use InvalidArgumentException;
use Exception;

class OperatingSystemVersion extends AbstractModel
{
    private $_id;
    private $_name;
    private $_code;
    private $_osId;

    /**
     * Get the unique ID for the OS Version
     * @return int
     * @throws Exception
     */
    public function getId(){
        if(!$this->_id){
            throw new Exception('OS Version ID has not been set.');
        }
        return $this->_id;
    }

    /**
     * Set the unique ID for the OS Version
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
     * Get the Identifying OS Version Name
     * @return string
     * @throws Exception
     */
    public function getName(){
        if(!$this->_name){
            throw new Exception('OS Version Name has not been set.');
        }
        return $this->_name;
    }

    /**
     * Sets the Identifying OS Version Name
     * @param string $name
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setName($name){
        self::_validateRequiredString($name);
        $this->_name = $name;
        return $this;
    }

    /**
     * Get the Identifying OS Version Code
     * @return string
     * @throws Exception
     */
    public function getCode(){
        if(!$this->_code){
            throw new Exception('OS Version Code has not been set.');
        }
        return $this->_code;
    }

    /**
     * Sets the Identifying OS Version Code
     * @param string $code
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setCode($code){
        self::_validateRequiredString($code);
        $this->_code = $code;
        return $this;
    }

    /**
     * Get the ID for the associated OS
     * @return int
     * @throws Exception
     */
    public function getOsId(){
        if(!$this->_osId){
            throw new Exception('OS ID has not been set.');
        }
        return $this->_osId;
    }

    /**
     * Set the ID for the associated OS
     * @param int $osId
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setOsId($osId){
        self::_validateRequiredInt($osId);
        $this->_osId = $osId;
        return $this;
    }

    /**
     * Return Simple Format for displaying OS info
     * @return string
     */
    public function toString(){
        return 'Operating System Version'.$this->_id.': '.$this->_name;
    }

    /**
     * Return object as formatted array
     * @return array
     */
    public function toArray(){
        $result = array();
        $result['os_version_id'] = $this->_id;
        $result['os_version_name'] = $this->_name;
        $result['os_version_code'] = $this->_code;
        $result['os_id'] = $this->_osId;
        return $result;
    }
}