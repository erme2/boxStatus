<?php
namespace boxStatus\Services;

use boxStatus\Controllers\Ancestor;
use Silex\Application;

Class tokenService extends Ancestor
{
    var $token  = false;
    var $time   = 0;
    var $expire = false;    // if expire is not setted in the config file we don't check it

    public function checkToken($token, $time)
    {
        // save params
        $this->token    = $token;
        $this->time     = abs($time);
        $this->expire   = abs($this->app['config']['token']['expire']) > 0 ? $this->app['config']['token']['expire'] : false;
        $this->diff     = (time() - abs($this->time));

        if(
            // TODO check time
            $this->_isNotExpired() &&
            // TODO check token
            $this->_tokenIsValid()
        ) {
            return true;
        }
        return false;
    }

    private function _isNotExpired()
    {
        // do we have to check time?
        if($this->expire) {
            if($this->diff > $this->expire){
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }

    }

    private function _tokenIsValid()
    {

    }
}
