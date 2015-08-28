<?php
/**
 * Created by IntelliJ IDEA.
 * User: Andy
 * Date: 28/08/2015
 * Time: 10:14
 */

namespace Synx\Resource;

use Synx\Model\Server;
use Exception;

trait ServerConnection
{
    /**
     * Connects to the server $server and runs the command $cmd, if rootAccess is required, it will fail if it cannot connect appropriately
     * @param Server $server
     * @param $cmd
     * @param bool|false $rootAccess
     * @return string
     * @throws Exception
     */
    public function runCommand(Server $server, $cmd, $rootAccess = false){
        $methods = array();
        if(!$server->isPasswordSet()){
            $methods = array('hostkey', 'ssh-rsa');
        }

        @$connection = ssh2_connect($server->getIp(), $server->getPort(), $methods);
        if(!($connection)){
            throw new Exception("Unable to establish connection to server [".$server->getIp()."], please Check IP or if server is on and connected");
        }

        if(!$server->isPasswordSet() && !$rootAccess){
            $rsa_pub = realpath(__DIR__.'/../ssh_keys/id_rsa.pub');
            $rsa = realpath(__DIR__.'../ssh_keys/id_rsa');

            if(!file_exists($rsa_pub)){
                throw new Exception("Public key file is not set up. Please add the file to the ssh_keys directory.");
            }

            if(!file_exists($rsa)){
                throw new Exception("Private key file is not set up. Please add the file to the ssh_keys directory.");
            }

            //ToDo: Accept alternate user names
            if(!ssh2_auth_pubkey_file($connection, 'sysad',$rsa_pub, $rsa)){
                throw new Exception("Unable to establish connection to server [".$server->getIp()."] using Public Key");
            }
        }else{
            if(!ssh2_auth_password($connection, 'root', $server->getPassword())) {
                throw new Exception("Unable to establish connection to server [".$server->getIp()."] using Password");
            }
        }


        $stream = ssh2_exec($connection, $cmd);
        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
        stream_set_blocking($errorStream, true);
        stream_set_blocking($stream, true);

        $response = stream_get_contents($stream);

        fclose($errorStream);
        fclose($stream);
        fclose($rsa_pub);
        fclose($rsa);
        unset($connection);

        return $response;
    }
}