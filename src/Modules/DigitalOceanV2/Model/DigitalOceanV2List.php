<?php

Namespace Model;

class DigitalOceanV2List extends BaseDigitalOceanV2AllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Listing") ;

    public function __construct($params) {
        parent::__construct($params) ;
    }

    public function askWhetherToListData() {
        return $this->performDigitalOceanV2ListData();
    }

    protected function performDigitalOceanV2ListData(){
        if ($this->askForListExecute() != true) { return false; }
        $this->accessToken = $this->askForDigitalOceanV2AccessToken();
        $dataToList = $this->askForDataTypeToList();
//        if ($dataToList == "images") {
//            // $it = (isset($this->params["guess"]) && $this->params["guess"]==true) ? "None" : $this->askForImageDistroToList() ;
//            $it = $this->askForImageDistroToList() ;
//            if ($it != "None") {
//                return $this->getDataListFromDigitalOceanV2($dataToList,
//                    array(/*"page"=>"1", */"per_page" =>100, "type" =>"distribution")); } }
        return $this->getDataListFromDigitalOceanV2($dataToList, array("per_page" =>100));
    }

    private function askForListExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'List Data?';
        return self::askYesOrNo($question);
    }

    private function askForDataTypeToList(){
        $question = 'Please choose a data type to list:';
        $options = array("droplets", "sizes", "images", "domains", "regions", "ssh_keys");
        if (isset($this->params["type"]) &&
            in_array($this->params["type"], $options)) {
            return $this->params["type"] ; }
        return self::askForArrayOption($question, $options, true);
    }

    private function askForImageDistroToList(){
        $question = 'Please choose an image distribution to list:';
        $options = array("Ubuntu", "Fedora", "CentOS", "Windows", "None");
        if (isset($this->params["distro"]) &&
            in_array($this->params["distro"], $options)) {
            return $this->params["distro"] ; }
        return self::askForArrayOption($question, $options, true);
    }

    public function getDataListFromDigitalOceanV2($dataToList, $callVars = array()){
        $curlUrl = $this->_apiURL."/v2/$dataToList" ;
        $httpType = "GET" ;
        return $this->digitalOceanV2Call($callVars, $curlUrl, $httpType);
    }

}