<?php
/**
 * Created by IntelliJ IDEA.
 * User: Andy
 * Date: 21/08/2015
 * Time: 16:12
 */

namespace Synx\Model;

use InvalidArgumentException;

abstract class AbstractModel
{
    abstract function toString();

    abstract function toArray();

    /**
     * Validates that the $string value is a string and trims the content, throwing exceptions if invalid. The allowBlank value returns an exception if a blank string is passed in but not allowed
     * @param string $string
     * @param bool $allowBlank
     * @throws InvalidArgumentException
     */
    protected final static function _validateRequiredString(&$string, $allowBlank = false)
    {
        if(!is_string($string)){
            throw new InvalidArgumentException(self::class.' value is not a string');
        }
        $string = trim($string);
        if(!$string){
            throw new InvalidArgumentException(self::class.' value is null.');
        }
    }

    /**
     * Validates that the $int value is an int and converts to int if string, throwing exceptions if invalid. The allowZero value returns an exception if zero is passed in but not allowed
     * @param mixed $int
     * @param bool $allowZero
     * @throws InvalidArgumentException
     */
    protected final static function _validateRequiredInt(&$int, $allowZero = false)
    {
        if(is_string($int)){
            $int = (intval($int));
        }
        if(!is_int($int)){
            throw new InvalidArgumentException(self::class.' value is not a int');
        }
        if(!$int && !$allowZero){
            throw new InvalidArgumentException(self::class.' value is zero.');
        }
    }

    /**
     * Validates that the $ip value is a IP address, throwing exceptions if invalid.
     * @param string $ip
     * @throws InvalidArgumentException
     */
    protected final static function _validateRequiredIp(&$ip)
    {
        if(!is_string($ip)){
            throw new InvalidArgumentException(self::class.' IP is not a string');
        }
        $ip = trim($ip);
        if(!$ip){
            throw new InvalidArgumentException(self::class.' IP is null');
        }
        if(!filter_var($ip,FILTER_VALIDATE_IP)){
            throw new InvalidArgumentException(self::class.' IP is not valid');
        }
    }


    /**
     * Validates that the $date value is a valid Date, throwing exceptions if invalid.
     * @param string $date
     * @throws InvalidArgumentException
     */
    protected final static function _validateRequiredDate(&$date)
    {
        if(!is_string($date)){
            throw new InvalidArgumentException(self::class.' Date is not a string');
        }
        $date = trim($date);
        if(!$date){
            throw new InvalidArgumentException(self::class.' Date is null');
        }
        $date = date('Y-m-d', strtotime($date));
    }
}