<?php

Namespace Model;

class DigitalOceanSshKey extends BaseDigitalOceanAllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("SshKey") ;

    public function askWhetherToSaveSshKey($params=null) {
        return $this->performDigitalOceanSaveSshKey($params);
    }

    public function performDigitalOceanSaveSshKey($params=null){
        if ($this->askForSSHKeyExecute() != true) { return false; }
        $this->apiKey = $this->askForDigitalOceanAPIKey();
        $this->clientId = $this->askForDigitalOceanClientID();
        $fileLocation = $this->askForSSHKeyPublicFileLocation();
        $fileData = file_get_contents($fileLocation);
        $keyName = $this->askForSSHKeyNameForDigitalOcean();
        return $this->saveSshKeyToDigitalOcean($fileData, $keyName);
    }

    private function askForSSHKeyExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Save local SSH Public Key file to Digital Ocean?';
        return self::askYesOrNo($question);
    }

    private function askForSSHKeyPublicFileLocation() {
        if (isset($this->params["digital-ocean-ssh-key-path"]) && $this->params["digital-ocean-ssh-key-path"]==true) {
            return $this->params["digital-ocean-ssh-key-path"] ; }
        $question = 'Enter Location of ssh public key file to upload';
        return self::askForInput($question, true);
    }

    private function askForSSHKeyNameForDigitalOcean(){
        if (isset($this->params["digital-ocean-ssh-key-name"]) && $this->params["digital-ocean-ssh-key-name"]==true) {
            return $this->params["digital-ocean-ssh-key-name"] ; }
        $question = 'Enter name to store ssh key under on Digital Ocean';
        return self::askForInput($question, true);
    }

    public function saveSshKeyToDigitalOcean($keyData, $keyName){
        $callVars = array();
        $keyData = str_replace("\n", "", $keyData);
        $callVars["ssh_pub_key"] = urlencode($keyData);
        $callVars["name"] = $keyName;
        $curlUrl = "https://api.digitalocean.com/v1/ssh_keys/new" ;
        return $this->digitalOceanCall($callVars, $curlUrl);
    }

}