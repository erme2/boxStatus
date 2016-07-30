<?php
namespace boxStatus\Services;

use boxStatus\Controllers\Ancestor;
use Silex\Application;

Class tokenService extends Ancestor
{
    var $token      = false;
    var $time       = 0;
    var $expire     = false;                    // if expire is not setted in the config file we don't check it
    var $saltKey    = "this is not a secret";   // please set it in the config file

    public function checkToken($token, $time)
    {
        // save params
        $this->token    = $token;
        $this->time     = $time;
        $this->expire   = abs($this->app['config']['token']['expire']) > 0  ? $this->app['config']['token']['expire']   : false;
        $this->saltKey  = isset($this->app['config']['token']['saltkey'])   ? $this->app['config']['token']['saltkey']  : $this->saltKey;
        $this->diff     = (time() - abs($this->time));

        if(
            // check time
            $this->_isNotExpired() &&
            // check token
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
                // expired
                return false;
            } else {
                // still valid
                return true;
            }
        } else {
            // always valid
            return true;
        }

    }

    private function _tokenIsValid()
    {
        if($this->token == $this->_getValidToken()){
            // token is valid
            return true;
        } else {
            // token is invalid
            return false;
        }
    }

    private function _getValidToken()
    {
        return md5("$this->time@$this->saltKey");
    }

}
