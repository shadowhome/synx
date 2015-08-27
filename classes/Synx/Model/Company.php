<?php
/**
 * User: Andy Abbott
 * Date: 24/08/2015
 * Time: 09:10
 */

namespace Synx\Model;

use InvalidArgumentException;
use Exception;

class Company extends AbstractModel
{
    private $_id;
    private $_name;

    /**
     * Get the unique ID for the Company
     * @return int
     * @throws Exception
     */
    public function getId(){
        if(!$this->_id){
            throw new Exception('Company ID has not been set.');
        }
        return $this->_id;
    }

    /**
     * Set the unique ID for the Company
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
     * Get the Identifying Company Name
     * @return string
     * @throws Exception
     */
    public function getName(){
        if(!$this->_name){
            throw new Exception('Company Name has not been set.');
        }
        return $this->_name;
    }

    /**
     * Sets the Identifying Company Name
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
     * Return Simple Format for displaying Company info
     * @return string
     */
    public function toString(){
        return 'Company '.$this->_id.': '.$this->_name;
    }

    /**
     * Return object as formatted array
     * @return array
     */
    public function toArray(){
        $result = array();
        $result['company_id'] = $this->_id;
        $result['company_name'] = $this->_name;
        return $result;
    }
}