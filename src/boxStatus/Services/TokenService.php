<?php
namespace boxStatus\Services;

use boxStatus\Controllers\Ancestor;

Class TokenService extends AncestorService
{
    var $token      = false;
    var $time       = 0;
    var $expire     = false;                    // if expire is not set in the config file we will not check it
    var $saltKey    = "this is not a secret";   // please set it in the config file

    /**
     * Checks if a token is valid
     *
     * @param $token
     * @param $time
     * @return bool
     */
    public function checkToken($token, $time)
    {
        // check config file
        if(empty($this->app['config']['token'])) {
            // there is an error on the config - token is requested but there are no settings
            // TODO save thir error somewhere
        }

        // get all params
        $this->token    = $token;
        $this->time     = $time;
        $this->expire   = isset($this->app['config']['token']['expire']) && abs($this->app['config']['token']['expire']) > 0  ? $this->app['config']['token']['expire'] : false;
        $this->saltKey  = isset($this->app['config']['token']['saltkey'])   ? $this->app['config']['token']['saltkey'] : $this->saltKey;
        $this->diff     = (time() - abs($this->time));

        // check
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

    /**
     * Checks if the token is expired
     *
     * @return bool
     */
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

    /**
     * Checks if the token is valid
     *
     * @return bool
     */
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


    /**
     * This function is for debug purpose only,
     * it generates a valid token
     *
     * @return string
     */
    private function _getValidToken()
    {
        return md5("$this->time@$this->saltKey");
    }

}
