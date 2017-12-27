<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/27
 * Time: 16:13
 */

namespace Leonis\Digiccy\Traits;

trait Help
{
    //获取erc20资产的信息
    protected function getErcInfo($name)
    {
        return (require_once(dirname(dirname(__FILE__)) . '/Config/erc.php'))[strtolower($name)];
    }

}