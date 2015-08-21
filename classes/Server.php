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

class Server extends AbstractModel
{
    private $_id;
    private $_name;
    private $_ip;
    private $_port;
    //ToDo: Move company into separate model and DB table
    private $_company = '';
    private $_description = '';
    private $_osName = '';
    private $_osVersionCode = '';
    private $_osVersionName = '';
    private $_password;

    /**
     * Get the unique ID for the server
     * @return int
     * @throws Exception
     */
    public function getId(){
        if(!$this->_id){
            throw new Exception('Server ID has not been set.');
        }
        return $this->_id;
    }

    /**
     * Set the unique ID for the server
     * @param int $id
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setId($id){
        if(!is_int($id)){
            throw new InvalidArgumentException('Server ID is not an integer');
        }
        if(!$id){
            throw new InvalidArgumentException('Server ID is null.');
        }
        $this->_id = $id;
        return $this;
    }

    /**
     * Get the Identifying Server Name
     * @return string
     * @throws Exception
     */
    public function getName(){
        if(!$this->_name){
            throw new Exception('Server Name has not been set.');
        }
        return $this->_name;
    }

    /**
     * Sets the Identifying Server Name
     * @param string $name
     * @return $this
     * @throws Exception
     */
    public function setName($name){
        if(!is_string($name)){
            throw new InvalidArgumentException('Server Name is not a string');
        }
        $name = trim($name);
        if(!$name){
            throw new InvalidArgumentException('Server Name is null.');
        }
        $this->_name = $name;
        return $this;
    }

    /**
     * Get the Server IP Address (Either IPv4 or IPv6)
     * @return string
     * @throws Exception
     */
    public function getIp()
    {
        if(!$this->_ip){
            throw new Exception('Server IP has not been set.');
        }
        return $this->_ip;
    }

    /**
     * Set the Server IP Address (Either IPv4 or IPv6)
     * @param string $ip
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setIp($ip)
    {
        if(!is_string($ip)){
            throw new InvalidArgumentException('Server IP is not a string');
        }
        $ip = trim($ip);
        if(!$ip){
            throw new InvalidArgumentException('Server IP is null');
        }
        if(!filter_var($ip,FILTER_VALIDATE_IP)){
            throw new InvalidArgumentException('Server IP is not valid');
        }
        $this->_ip = $ip;
        return $this;
    }

    /**
     * Get the server port
     * @return mixed
     * @throws Exception
     */
    public function getPort()
    {
        if(!$this->_port){
            throw new Exception('Server Port has not been set');
        }
        return $this->_port;
    }

    /**
     * Set the server port
     * @param $port
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setPort($port){
        if(!is_int($port)){
            throw new InvalidArgumentException('Server Port is not an integer');
        }
        $this->_port = $port;
        return $this;
    }

    /**
     * Get the Server Company Name
     * @return string
     */
    public function getCompany()
    {
        return $this->_company;
    }

    /**
     * Set the Server Company Name
     * @param string $company
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setCompany($company)
    {
        if(!is_string($company)){
            throw new InvalidArgumentException('Server Company is not a string');
        }
        $this->_company = trim($company);
        return $this;
    }

    /**
     * Get the server description
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set the server description
     * @param string $description
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setDescription($description)
    {
        if(!is_string($description)){
            throw new InvalidArgumentException('Server Description is not a string');
        }
        $this->_description = trim($description);
        return $this;
    }

    /**
     * Get the Server Operating System Reference
     * @return string
     */
    public function getOsName()
    {
        return $this->_osName;
    }

    /**
     * Set the Server Operating System Reference
     * @param string $osName
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setOsName($osName)
    {
        if(!is_string($osName)){
            throw new InvalidArgumentException('Server Operating System Name is not a string');
        }
        $this->_osName = trim($osName);
        return $this;
    }

    /**
     * Get the Server Operating System Version
     * @return string
     */
    public function getOsVersionCode()
    {
        return $this->_osVersionCode;
    }

    /**
     * Set the Server Operating System Version
     * @param string $osVersionCode
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setOsVersionCode($osVersionCode)
    {
        if(!is_string($osVersionCode)){
            throw new InvalidArgumentException('Server Operating System Version Code is not a string');
        }
        $this->_osVersionCode = trim($osVersionCode);
        return $this;
    }

    /**
     * Get the Server Operating System Version Reference
     * @return string
     */
    public function getOsVersionName()
    {
        return $this->_osVersionName;
    }

    /**
     * Set the Server Operating System Reference
     * @param string $osVersionName
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setOsVersionName($osVersionName)
    {
        if(!is_string($osVersionName)){
            throw new InvalidArgumentException('Server Operating System Version Name is not a string');
        }
        $this->_osVersionName = trim($osVersionName);
        return $this;
    }

    /**
     * Get the password for the server
     * @return mixed
     * @throws Exception
     */
    public function getPassword(){
        if(!$this->_password){
            throw new Exception('The server password has not been set');
        }
        return $this->_password;
    }

    /**
     * Set the password for the server
     * @param $password
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setPassword($password)
    {
        if (!is_string($password)) {
            throw new InvalidArgumentException('Server Password is not a string');
        }
        $this->_password = trim($password);
        return $this;
    }

    public function isPasswordSet(){
        return isset($this->_password) && $this->_password;
    }

    /**
     * Return Simple Format for displaying server info
     * @return string
     */
    public function toString(){
        return 'Server '.$this->_id.': '.$this->_name.' ['.$this->_ip.']';
    }

    /**
     * Return object as formatted array
     * @return array
     */
    public function toArray(){
        $result = array();
        $result['server_id'] = $this->_id;
        $result['server_name'] = $this->_name;
        $result['ip'] = $this->_ip;
        $result['port'] = $this->_port;
        $result['company'] = $this->_company;
        $result['description'] = $this->_description;
        $result['os_name'] = $this->_osName;
        $result['os_version_code'] = $this->_osVersionCode;
        $result['os_version_name'] = $this->_osVersionName;
        return $result;
    }
}