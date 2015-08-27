<?php
/**
 * User: Andy Abbott
 * Date: 21/08/2015
 * Time: 09:34
 */

namespace Synx\Controller;

use PDO;
use PDOException;
use InvalidArgumentException;

abstract class AbstractController{
    private $_connection;

    /**
     * Get the PDO Database Connection
     * @return PDO
     * @throws PDOException
     */
    protected function getDbConnection(){
        $config_path = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'upconfig.php';
        $config_path = realpath($config_path);

        //ToDo: Error handling for when config not setup
        require $config_path;

        if(isset($this->_connection)){
            return $this->_connection;
        }
        //Set Sqlite DB to file system
        $this->_connection = new PDO('mysql:dbname='.$db_name.';host='.$db_host, $db_user, $db_pass);
        //Set DB Exception Handling
        $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->_connection;
    }

    protected static function filterParams(&$params, $keys){
        if(!(is_array($params)) || empty($params)){
            throw new InvalidArgumentException('Parameter Filter: Parameters are invalid');
        }
        if(!(is_array($keys)) || empty($keys)){
            throw new InvalidArgumentException('Parameter Filter: Keys are invalid');
        }

        $params = array_intersect_key($params, array_flip($keys));
    }
}