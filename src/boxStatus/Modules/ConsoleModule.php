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

    public function getDinamic($configs)
    {
        if(
            isset($configs['result']['human']) &&
            $configs['result']['human']
        ) $human = true;
        else $human = false;

        if(
            isset($configs['disks']) &&
            is_array($configs['disks'])
        ) $disks = $configs['disks'];
        else $disks = ['main' => '/',];

        $return['cpu']   = $this->_getCPU();
        $return['disks'] = $this->_getDisks($disks, $human);
        $return['ram']   = $this->_getMemory($human);
        $return['swap']  = $this->_getSwap($human);
        // TODO add uptime
        // TODO add system updates

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

    public function humanSize($Bytes)
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

    public function smartExplode($line){
        $badChars = ["\n"];
        $goodChars = [""];

        $line = str_replace($badChars, $goodChars, $line);

        $return = explode(" ", $line);
        foreach ($return as $key=>$one) {
            if (trim($one) == ""){
                unset($return[$key]);
            }
        }
        return array_values($return);
    }

    private function _getCPU()
    {
        $return = [
            '%cpu' => 0,
            '%mem' => 0,
        ];
        $lines = $this->_shellExec("ps aux");
        foreach ($lines as $row => $line) {
            if ($row > 0) { // skipping the title
                $data = $this->smartExplode($line);
                $return['%cpu'] = $return['%cpu'] + $data[2];
                $return['%mem'] = $return['%mem'] + $data[3];
            }
        }
        $return['load']  = sys_getloadavg();
        return $return;
    }

    private function _getDisks(Array $disks, $human = false)
    {
        $return = [];
        $list = $this->_shellExec("df");
        foreach ($list as $line){
            $device = $this->smartExplode($line);
            // considering just used disks
            if(in_array($device[5], $disks)){
                $return[$device[5]]['FileSystem']   = $device[0];
                $return[$device[5]]['Total']        = $device[1] * 1024;
                $return[$device[5]]['Used']         = $device[2] * 1024;
                $return[$device[5]]['Available']    = $device[3] * 1024;
                $return[$device[5]]['UsedPerc']     = $device[4];
                if($human) {
                    $return[$device[5]]['human']['Total']        = $this->humanSize($device[1] * 1024);
                    $return[$device[5]]['human']['Used']         = $this->humanSize($device[2] * 1024);
                    $return[$device[5]]['human']['Available']    = $this->humanSize($device[3] * 1024);
                }
            }
        }

        return $return;
    }

    private function _getHost()
    {
        // TODO find a name to this function
        $output = $this->_shellExec("hostnamectl");

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
                    $return['human'][$key] =$this->humanSize($one);
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

        $output = $this->_shellExec("ifconfig");

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
        $return = [];
        if (is_readable($this->procSwapsFile)) {
            $handle = fopen($this->procSwapsFile, 'r');
            $lines = [];
            if ($handle) {
                while ($line = fgets($handle)) {
                    array_push($lines, $line);
                }
                if(count($lines) > 1){
                    foreach ($lines as $row=>$line) {
                        if ($row > 0) { // skipping the title
                            $data = $this->smartExplode($line);
                            $return[$data[0]] = [
                                'Type' => $data[1],
                                'Size' => $data[2] * 1024,
                                'Used' => $data[3] * 1024,
                                'Priority' => $data[4],
                            ];
                            if ($human) {
                                $return[$data[0]]['Human'] = [
                                    'Size' => $this->humanSize($data[2] * 1024),
                                    'Used' => $this->humanSize($data[3] * 1024),
                                ];
                            }
                        }
                    }
                }
            }

            if($human) {
                foreach ($return['devices'] as $key=>$item) {
                    $return['devices'][$key]['SizeH'] = $this->humanSize($return['devices'][$key]['Size']);
                    $return['devices'][$key]['UsedH'] = $this->humanSize($return['devices'][$key]['Used']);
                }
            }

            return $return;
        }
        return [
            'error' => "$this->procSwapsFile not readable",
        ];
    }

    private function _shellExec($command, $escape = false)
    {
        if($escape) {
            $command = $command . escapeshellarg($escape);
        }

        $res = exec($command, $output, $return_var);

        return $output;
    }

}