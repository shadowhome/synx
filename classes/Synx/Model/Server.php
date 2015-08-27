<?php
/**
 * User: Andy Abbott
 * Date: 20/08/2015
 * Time: 12:05
 */
namespace Synx\Model;

use InvalidArgumentException;
use Exception;

class Server extends AbstractModel
{
    const DEFAULT_PORT = 22;
    private $_id;
    private $_name;
    private $_ip;
    private $_port;
    private $_description = '';
    private $_password;
    private $_companyId;
    private $_osVersionId;
    private $_ram;
    private $_cpuFrequency;
    private $_cpuArchitecture;
    private $_cpuNumber;
    private $_cpuSockets;
    private $_cpuThreads;
    private $_cpuCore;

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
        self::_validateRequiredInt($id);
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
        self::_validateRequiredString($name);
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
        self::_validateRequiredIp($ip);
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
            return self::DEFAULT_PORT;
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
        self::_validateRequiredInt($port);
        $this->_port = $port;
        return $this;
    }

    /**
     * Get the associated Company Id
     * @return int
     * @throws Exception
     *
     */
    public function getCompanyId()
    {
        if(!$this->_companyId){
            throw new Exception('Server Company ID has not been set.');
        }
        return $this->_companyId;
    }

    /**
     * Set the associated Company Id
     * @param int $companyId
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setCompanyId($companyId)
    {
        self::_validateRequiredInt($companyId);
        $this->_companyId = $companyId;
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
        self::_validateRequiredString($description, true);

        $this->_description = $description;
        return $this;
    }

    /**
     * Get the associated Operating System Version Id
     * @return int
     * @throws Exception
     */
    public function getOsVersionId()
    {
        if(!$this->_osVersionId){
            throw new Exception('Server OS Version ID has not been set.');
        }
        return $this->_osVersionId;
    }

    /**
     * Set the associated Operating System Version Id
     * @param int $osVersionId
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setOsVersionId($osVersionId)
    {
        self::_validateRequiredInt($osVersionId);
        $this->_osVersionId = $osVersionId;
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
        self::_validateRequiredString($password);
        return $this;
    }

    public function isPasswordSet(){
        return isset($this->_password) && $this->_password;
    }

    /**
     * Get the amount of RAM for the server
     * @return int
     */
    public function getRam()
    {
        return $this->_ram;
    }

    /**
     * Set the amount of RAM for the server
     * @param int $ram
     * @return Server
     */
    public function setRam($ram)
    {
        self::_validateRequiredInt($ram);
        $this->_ram = $ram;
        return $this;
    }

    /**
     * Get the CPU Frequency for the server
     * @return int
     */
    public function getCpuFrequency()
    {
        return $this->_cpuFrequency;
    }

    /**
     * Set the CPU Frequency for the server
     * @param int $cpuFrequency
     * @return Server
     */
    public function setCpuFrequency($cpuFrequency)
    {
        self::_validateRequiredInt($cpuFrequency);
        $this->_cpuFrequency = $cpuFrequency;
        return $this;
    }

    /**
     * Get the CPU Architecture
     * @return string
     */
    public function getCpuArchitecture()
    {
        return $this->_cpuArchitecture;
    }

    /**
     * Set the CPU Architecture
     * @param string $cpuArchitecture
     * @return Server
     */
    public function setCpuArchitecture($cpuArchitecture)
    {
        self::_validateRequiredString($cpuArchitecture);
        $this->_cpuArchitecture = $cpuArchitecture;
        return $this;
    }

    /**
     * Get the CPU Number
     * @return string
     */
    public function getCpuNumber()
    {
        return $this->_cpuNumber;
    }

    /**
     * Set the CPU Number
     * @param string $cpuNumber
     * @return Server
     */
    public function setCpuNumber($cpuNumber)
    {
        self::_validateRequiredString($cpuNumber,true);
        $this->_cpuNumber = $cpuNumber;
        return $this;
    }

    /**
     * Get the CPU Sockets
     * @return string
     */
    public function getCpuSockets()
    {
        return $this->_cpuSockets;
    }

    /**
     * Set the CPU Sockets
     * @param string $cpuSockets
     * @return Server
     */
    public function setCpuSockets($cpuSockets)
    {
        self::_validateRequiredString($cpuSockets, true);
        $this->_cpuSockets = $cpuSockets;
        return $this;
    }

    /**
     * Get the CPU Threads
     * @return string
     */
    public function getCpuThreads()
    {
        return $this->_cpuThreads;
    }

    /**
     * Set the CPU Threads
     * @param string $cpuThreads
     * @return Server
     */
    public function setCpuThreads($cpuThreads)
    {
        self::_validateRequiredString($cpuThreads, true);
        $this->_cpuThreads = $cpuThreads;
        return $this;
    }

    /**
     * Get the CPU Core
     * @return string
     */
    public function getCpuCore()
    {
        return $this->_cpuCore;
    }

    /**
     * Set the CPU Core
     * @param string $cpuCore
     * @return Server
     */
    public function setCpuCore($cpuCore)
    {
        self::_validateRequiredString($cpuCore, true);
        $this->_cpuCore = $cpuCore;
        return $this;
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
        $result['description'] = $this->_description;
        $result['company_id'] = $this->_companyId;
        $result['os_version_id'] = $this->_osVersionId;
        $result['ram'] = $this->_ram;
        $result['cpu_frequency'] = $this->_cpuFrequency;
        $result['cpu_architecture'] = $this->_cpuArchitecture;
        $result['cpu_number'] = $this->_cpuNumber;
        $result['cpu_sockets'] = $this->_cpuSockets;
        $result['cpu_threads'] = $this->_cpuThreads;
        $result['cpu_core'] = $this->_cpuCore;

        return $result;
    }
}