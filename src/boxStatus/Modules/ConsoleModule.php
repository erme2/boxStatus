<?php
namespace boxStatus\Modules;


Class ConsoleModule
{
    var $system = [];
    var $network = [];

    public function getNetwork()
    {
        $command = escapeshellarg("hostnamectl");
        $a = exec ($command, $output ,$return_var);

        $this->system['hostname'] = substr($output[0], strpos($output[0], ":")+2);
        $this->system['os'] = substr($output[6], strpos($output[6], ":")+2);


print_r($this);
print_r($output);
        die;


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


}