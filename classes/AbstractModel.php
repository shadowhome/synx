<?php
/**
 * Created by IntelliJ IDEA.
 * User: Andy
 * Date: 21/08/2015
 * Time: 16:12
 */

namespace Synx\Model;

abstract class AbstractModel
{
    abstract function toString();

    abstract function toArray();
}