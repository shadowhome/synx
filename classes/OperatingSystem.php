<?php
/**
 * User: Andy Abbott
 * Date: 24/08/2015
 * Time: 08:57
 */

namespace Synx\Model;

include_once 'AbstractModel.php';

use InvalidArgumentException;
use Exception;

class OperatingSystem extends AbstractModel
{
    private $_id;
    private $_name;

    /**
     * Get the unique ID for the OS
     * @return int
     * @throws Exception
     */
    public function getId(){
        if(!$this->_id){
            throw new Exception('OS ID has not been set.');
        }
        return $this->_id;
    }

    /**
     * Set the unique ID for the OS
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
     * Get the Identifying OS Name
     * @return string
     * @throws Exception
     */
    public function getName(){
        if(!$this->_name){
            throw new Exception('OS Name has not been set.');
        }
        return $this->_name;
    }

    /**
     * Sets the Identifying OS Name
     * @param string $name
     * @return $this
     * @throws Exception
     */
    public function setName($name){
        self::_validateRequiredString($name);
        $this->_name = $name;
        return $this;
    }

    /**
     * Return Simple Format for displaying OS info
     * @return string
     */
    public function toString(){
        return 'Operating System '.$this->_id.': '.$this->_name;
    }

    /**
     * Return object as formatted array
     * @return array
     */
    public function toArray(){
        $result = array();
        $result['os_id'] = $this->_id;
        $result['os_name'] = $this->_name;
        return $result;
    }
}