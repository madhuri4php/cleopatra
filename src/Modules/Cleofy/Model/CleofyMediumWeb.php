<?php

Namespace Model;

// @todo shouldnt this extend base templater? is it missing anything?
class CleofyMediumWeb extends Base {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("Debian") ;
    public $distros = array("Ubuntu") ;
    public $versions = array("12.04", "12.10") ;
    public $architectures = array("32", "64") ;

    // Model Group
    public $modelGroup = array("MediumWeb") ;

    private $environments ;
    private $environmentReplacements ;

    public function __construct($params) {
      parent::__construct($params);
    }

    public function askWhetherToCleofy() {
        if ($this->askToScreenWhetherToCleofy() != true) { return false; }
        $this->setEnvironmentReplacements() ;
        $this->getEnvironments() ;
        $this->doCleofy() ;
        return true;
    }

    public function askToScreenWhetherToCleofy() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Cleofy This?';
        return self::askYesOrNo($question, true);
    }

    public function setEnvironmentReplacements() {
        $this->environmentReplacements =
            array( "cleo" => array(
               // array("var"=>"dap_proj_cont_dir", "friendly_text"=>"Project Container directory, (inc slash)"),
            ) );
    }

    public function getEnvironments() {
        $environmentConfigModelFactory = new EnvironmentConfig();
        $environmentConfigModel = $environmentConfigModelFactory->getModel($this->params);
        $environmentConfigModel->askWhetherToEnvironmentConfig($this->environmentReplacements) ;
        $this->environments = $environmentConfigModel->environments ;
    }

    public function getServerArrayText($serversArray) {
        $serversText = "";
        foreach($serversArray as $serverArray) {
            $serversText .= 'array(';
            $serversText .= '"target" => "'.$serverArray["target"].'", ';
            $serversText .= '"user" => "'.$serverArray["user"].'", ';
            $serversText .= '"pword" => "'.$serverArray["password"].'", ';
            $serversText .= '),'."\n"; }
        return $serversText;
    }

    private function doCleofy() {
        $templatesDir = str_replace("Model", "Templates/EnvSpecific", dirname(__FILE__) ) ;
        $templates = scandir($templatesDir);
        foreach ($this->environments as $environment) {

            if (isset($this->params["environment-name"])) {
                if ($this->params["environment-name"] != $environment["any-app"]["gen_env_name"]) {
                    $tx = "Skipping Environment {$environment["any-app"]["gen_env_name"]} " ;
                    $tx .= "as specified Environment is {$this->params["environment-name"]} \n" ;
                    echo $tx;
                    continue ; } }

            foreach ($templates as $template) {
              if (!in_array($template, array(".", ".."))) {
                  $templatorFactory = new \Model\Templating();
                  $templator = $templatorFactory->getModel($this->params);
                  $newFileName = str_replace("environment", $environment["any-app"]["gen_env_name"], $template ) ;
                  $autosDir = getcwd().'/build/config/cleopatra/cleofy/autopilots/generated';
                  $targetLocation = $autosDir.DIRECTORY_SEPARATOR.$newFileName ;
                  $templator->template(
                      file_get_contents($templatesDir.DIRECTORY_SEPARATOR.$template),
                      array(
                          "gen_srv_array_text" => $this->getServerArrayText($environment["servers"]) ,
                          "env_name" => $environment["any-app"]["gen_env_name"],
                          "first_server_target" => $environment["servers"][0]["target"],
                          "stage_web_nodes_env" => $this->getEnvName("stage web") ,
                          "prod_web_nodes_env" => $this->getEnvName("prod web") ,
                      ),
                      $targetLocation );
              echo $targetLocation."\n"; } } }
    }

    public function getEnvName($envType) {
        if (isset($this->params["$envType-nodes-env"])) {
            $this->params["$envType-nodes-environment"] = $this->params["$envType-nodes-env"] ; }
        if (isset($this->params["$envType-nodes-environment"])) { return $this->params["$envType-nodes-environment"] ; }
        $question = "Enter name of environment with your ".ucfirst($envType)." nodes" ;
        $this->params["$envType-nodes-environment"] = $this->askForInput($question) ;
        return $this->params["$envType-nodes-environment"] ;
    }

}