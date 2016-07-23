<?php
namespace boxStatus\Services;

use Silex\Application;
use Symfony\Component\Validator\Constraint as Assert;

Class ipService
{
    public function checkIpList($ipList)
    {
        // check white_list
        if($ipList['white_list']){
            return $this->_checkList($ipList['white_list'], true);
        } else {
        // or check black_list
            return $this->_checkList($ipList['black_list']);
        }

    }

    private function _checkList($list, $retun = false)
    {
        $this->setAddresses();
    }

    private function setAddresses()
    {
        $this->remoteAddress = $_SERVER['REMOTE_ADDR'];

        $errors = $app['validator']->validate($this->remoteAddress, Assert::);

        $numbers = explode(".", $this->remoteAddress);

        $this->level1 = "$numbers[0].*.*.*";
        $this->level2 = "$numbers[0].$numbers[0].*.*";
        $this->level3 = "$numbers[0].$numbers[1].$numbers[2].*";
        $this->level4 = "$numbers[0].$numbers[1].$numbers[2].$numbers[3]";
        print_r($this);
        die;

    }

}
