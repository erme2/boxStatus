<?php
namespace boxStatus\Modules;

use boxStatus\Controllers\Ancestor;

Class ConsoleModule extends Ancestor
{
    var $system = [];
    var $network = [];

    public function __construct()
    {
    }

    public function getNetwork()
    {



        $command = escapeshellarg("ifconfig");
        $a = exec ($command, $output ,$return_var);

        $return = [];
        foreach ($output as $line) {
            if(strpos($line, 'inet ')){
                $line = trim($line);
                $start = strpos($line, ":")+1;
                $ipLen = strpos($line, " Bcast") - $start;
                $address = trim(
                    substr($line, $start, $ipLen)
                );
                // unmasking localhost
                if(strpos($address, "Mask")){
                    $address = trim(str_replace("Mask", "", $address));
                }
                array_push($return, $address);
                echo "@$address#\n";
            }
        }
        print_r($return);

        die;




    }

    private function _toBeNominated() {
        // TODO find a name to this function
        $command = escapeshellarg("hostnamectl");
        $a = exec ($command, $output ,$return_var);

        // TODO save those data to a yml file
        // TODO write a fuction to load this data
        // TODO add a param to the main function to load those data and set them in the response (not defaul)
        // TODO write a function delete and reload those data
        // TODO add a param to the main function to fire the delete/reload
        $this->system['box']        = substr($output[5], strpos($output[5], ":")+2);
        $this->system['boxID']      = substr($output[3], strpos($output[3], ":")+2);
        $this->system['bootID']     = substr($output[4], strpos($output[4], ":")+2);
        $this->system['hostname']   = substr($output[0], strpos($output[0], ":")+2);
        $this->system['os']         = substr($output[6], strpos($output[6], ":")+2);
        $this->system['kernel']     = substr($output[7], strpos($output[7], ":")+2);

    }



}