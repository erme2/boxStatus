<?php
namespace boxStatus\Services;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

Class ipService
{
    var $remoteAddress      = false;
    var $remoteAddressMasks = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->setAddresses();
    }

    public function checkIpList($ipList)
    {
        // check white_list
        if(isset($ipList['white_list'])){
            return $this->_checkList($ipList['white_list']);
        } else {
        // or check black_list
            return $this->_checkList($ipList['black_list'], true);
        }
    }

    /**
     * This function is checking white list $expecting false as
     * default response, but if we find your address in the list
     * we reverse the $expecting value, and you are IN!
     *
     * On the other side the are $expecting true as default on a
     * black list check. As before if we find your ip we reverse
     * the $expecting and you're OUT!
     *
     * @param   array $list         the addresses array to check
     * @param   bool  $expecting    the secure response we expext
     * @return  bool
     */
    private function _checkList(array $list, $expecting = false)
    {
        foreach ($list as $item) {
            if(array_search($item, $this->remoteAddressMasks)) {
                // reversing $expecting value
                $expecting = $expecting ? false : true;
                return $expecting;
            }
        }
    }

    private function setAddresses()
    {
        $this->remoteAddress = $_SERVER['REMOTE_ADDR'];

        $errors = $this->app['validator']->validate($this->remoteAddress, new Assert\Ip());
        if($errors->count() > 0){
            $this->app->abort(500, "Internal Server Error");
        }

        // setting the masks
        $numbers = explode(".", $this->remoteAddress);
        $this->remoteAddressMasks['level1'] = "$numbers[0].*.*.*";
        $this->remoteAddressMasks['level2'] = "$numbers[0].$numbers[0].*.*";
        $this->remoteAddressMasks['level3'] = "$numbers[0].$numbers[1].$numbers[2].*";
        $this->remoteAddressMasks['level4'] = "$numbers[0].$numbers[1].$numbers[2].$numbers[3]";
    }

}
