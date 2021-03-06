<?php

Namespace Model;

class PingUbuntu extends BaseLinuxApp {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("Debian") ;
    public $distros = array("Ubuntu") ;
    public $versions = array("11.04", "11.10", "12.04", "12.10", "13.04") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;
    protected $target ;
    protected $interval ;
    protected $maxWait ;
    protected $actionsToMethods = array(
        "once" => "performPingOnce",
        "ten" => "performTenPings",
        "until-responding" => "performPingsUntil",
    ) ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "Ping";
        $this->programDataFolder = "";
        $this->programNameMachine = "ping"; // command and app dir name
        $this->programNameFriendly = "!Ping!!"; // 12 chars
        $this->programNameInstaller = "Ping";
        $this->initialize();
    }

    protected function performPingOnce() {
        $libDir = str_replace("Model", "Libraries", dirname(__FILE__) ) ;
        require_once ("{$libDir}/JJG/Ping.php") ;
        $this->setTarget();
        $this->setInterval();
        return $this->doOnePing();
    }

    protected function performTenPings() {
        $libDir = str_replace("Model", "Libraries", dirname(__FILE__) ) ;
        require_once ("{$libDir}/JJG/Ping.php") ;
        $this->setTarget();
        $this->setInterval();
        return $this->doTenPings();
    }

    protected function performPingsUntil() {
        $libDir = str_replace("Model", "Libraries", dirname(__FILE__) ) ;
        require_once ("{$libDir}/JJG/Ping.php") ;
        $this->setTarget();
        $this->setInterval();
        $this->setMaxWait();
        return $this->doPingsUntil();
    }

    protected function setTarget($target = null) {
        if (isset($target)) {
            $this->target = $target; }
        else if (isset($this->params["target"])) {
            $this->target = $this->params["target"]; }
        else {
            $this->target = self::askForInput("Enter Target: ", true); }
    }

    protected function setInterval() {
        if (isset($this->params["interval"])) {
            $this->interval = $this->params["interval"]; }
        else if (isset($this->params["guess"])) {
            $this->interval = "2" ; }
        else {
            $this->interval = self::askForInput("Enter Interval: ", true); }
    }

    protected function setMaxWait() {
        if (isset($this->params["max-wait"])) {
            $this->maxWait = $this->params["max-wait"]; }
        else if (isset($this->params["guess"])) {
            $this->maxWait = "60" ; }
        else {
            $this->maxWait = self::askForInput("Enter Max Wait Time: ", true); }
    }

    private function doOnePing() {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $ping = new \JJG\Ping($this->target);
        $latency = $ping->ping();
        if ($latency !== false) {
            $logging->log('Ping Latency is ' . $latency . ' ms') ;
            return true; }
        else {
            $logging->log("Ping Host {$this->target} could not be reached.") ;
            return false; }
    }

    private function doTenPings() {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $ping = new \JJG\Ping($this->target);
        for ($i = 0; $i < 10; $i ++) {
            $latency = $ping->ping();
            if ($latency !== false) {
                $logging->log('Ping Latency is ' . $latency . ' ms') ;
            $foundSuccess = true; }
            else {
                $time = $i * $this->interval ;
                $logging->log("Ping Host {$this->target} could not be reached after $i iterations and $time seconds") ; }
            sleep($this->interval) ; }
        if (isset ($foundSuccess) && $foundSuccess==true) { return true ; }
        return false ;
    }

    private function doPingsUntil() {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $ping = new \JJG\Ping($this->target);
        $totalTime = 0 ;
        $i = 0;
        while ($totalTime < $this->maxWait) {
            $latency = $ping->ping();
            if ($latency !== false) {
                $logging->log('Ping Latency is ' . $latency . ' ms') ;
                return true ; }
            else {
                $logging->log("Ping Host {$this->target} could not be reached after $i iterations and $totalTime seconds") ; }
            sleep($this->interval) ;
            $totalTime = $totalTime + $this->interval ;
            $i++; }
        return false ;
    }

}