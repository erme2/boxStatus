<?php
namespace boxStatus\Modules;

use boxStatus\Controllers\Ancestor;

Class ConsoleModule extends Ancestor
{
    var $system = [];
    var $network = [];
    var $procSwapsFile      = '/proc/swaps';
    var $procMeminfoFile    = '/proc/meminfo';

    public function __construct()
    {
    }

    public function HumanSize($Bytes)
    {
        $Type=array("bites", "kb", "mb", "gb", "tb", "peta", "exa", "zetta", "yotta");
        $Index=0;
        while($Bytes>=1024)
        {
            $Bytes/=1024;
            $Index++;
        }
        $Bytes = round($Bytes,2);
        return("$Bytes $Type[$Index]");
    }

    public function getDinamic($human = false)
    {
        $return['meminfo']['ram']   = $this->_getMemory($human);
        $return['meminfo']['swap']  = $this->_getSwap($human);
        return $return;
    }


    /**
     * this function use _getHost and _getNetwork
     * to read the static info
     * TODO add save those info in to a yml file
     * TODO add read those info from to a yml file
     */
    public function getStatic()
    {
        // TODO write it!
    }

    private function _getHost()
    {
        // TODO find a name to this function
        $command = escapeshellarg("hostnamectl");
        $a = exec($command, $output, $return_var);

        // TODO save those data to a yml file
        // TODO write a fuction to load this data
        // TODO add a param to the main function to load those data and set them in the response (not defaul)
        // TODO write a function delete and reload those data
        // TODO add a param to the main function to fire the delete/reload
        $this->system['box'] = substr($output[5], strpos($output[5], ":") + 2);
        $this->system['boxID'] = substr($output[3], strpos($output[3], ":") + 2);
        $this->system['bootID'] = substr($output[4], strpos($output[4], ":") + 2);
        $this->system['hostname'] = substr($output[0], strpos($output[0], ":") + 2);
        $this->system['os'] = substr($output[6], strpos($output[6], ":") + 2);
        $this->system['kernel'] = substr($output[7], strpos($output[7], ":") + 2);

    }

    private function _getMemory($human = false)
    {
        if (is_readable($this->procMeminfoFile)) {
            $handle = fopen($this->procMeminfoFile, 'r');
            $return = [];
            if ($handle) {
                while ($line = fgets($handle)){
                    $data = explode(":", $line);
                    switch ($data[0]){
                        case "MemTotal":
                        case "MemFree":
                        case "MemAvailable":
                            $return[$data[0]] = abs(str_replace("kB", '', $data[1])) * 1024;
                            break;
                    }
                    if($data[0]=='Buffers') {
                        break;
                    }
                }

                if($human){
                    foreach ($return as $key=>$one) {
                    $return['human'][$key] =$this->HumanSize($one);
                    }
                }

                return $return;
            }
        }
        return [
            'error' => "$this->procMeminfoFile not readable",
        ];
    }

    private function _getNetwork()
    {


        $command = escapeshellarg("ifconfig");
        $a = exec($command, $output, $return_var);

        $return = [];
        foreach ($output as $line) {
            if (strpos($line, 'inet ')) {
                $line = trim($line);
                $start = strpos($line, ":") + 1;
                $ipLen = strpos($line, " Bcast") - $start;
                $address = trim(
                    substr($line, $start, $ipLen)
                );
                // unmasking localhost
                if (strpos($address, "Mask")) {
                    $address = trim(str_replace("Mask", "", $address));
                }
                array_push($return, $address);
                echo "@$address#\n";
            }
        }
        print_r($return);

        die;


    }

    private function _getSwap($human = false)
    {
        if (is_readable($this->procSwapsFile)) {
            $handle = fopen($this->procSwapsFile, 'r');
            $return['devices'] = [];
            $lines = [];
            if ($handle) {
                while ($line = fgets($handle)) {
                    array_push($lines, $line);
                }
                if(count($lines) > 1){
                    foreach ($lines as $line){
                        $data = array_values(
                            array_filter(explode(" ", $line))
                        );
                        if($data[0] != 'Filename') {
                            if(count($data)>4) {
                                array_push($return['devices'], [
                                    'device' => $data[0],
                                    'type' => $data[1],
                                    'size' => $data[2] * 1024,
                                    'used' => $data[3] * 1024,
                                    'priority' => trim($data[4]),
                                ]);
                            } else {
                                array_push($return['devices'], [
                                    'Device' => $data[0],
                                    'Type' => $data[1],
                                    'Size' => $data[2] * 1024,
                                    'Used' => 0,
                                    'Priority' => trim($data[3]),
                                ]);
                            }
                        }
                    }
                }
            }

            if($human) {
                foreach ($return['devices'] as $key=>$item) {
                    $return['devices'][$key]['SizeH'] = $this->HumanSize($return['devices'][$key]['Size']);
                    $return['devices'][$key]['UsedH'] = $this->HumanSize($return['devices'][$key]['Used']);
                }
            }

            return $return;
        }
        return [
            'error' => "$this->procSwapsFile not readable",
        ];
    }
}