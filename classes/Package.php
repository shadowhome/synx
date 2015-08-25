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

    /**
     * Get the unique ID for the package
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
     * Set the unique ID for the package
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
     * get the unique name for the package
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
     * set the unique name for the package
     * @param string $name
     * @return Package
     */
    public function setName($name)
    {
        self::_validateRequiredString($name);
        $this->_name = $name;
        return $this;
    }

    function toString()
    {
        return 'Package '.$this->_id.': '.$this->_name;
    }

    function toArray()
    {
        $result = array();
        $result['package_id'] = $this->_id;
        $result['package_name'] = $this->_name;
        return $result;
    }
}