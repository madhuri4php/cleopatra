<?php

Namespace Controller ;

class TaskExecutor extends Base {

    public function executeTFTask($pageVars, $task) {

        $sourceParams = implode(" ", $this->formatParams($pageVars["route"]["extraParams"])) ;
        $tasks = $this->getTaskfileTasks($pageVars["route"]["extraParams"]) ;

        // var_dump($tasks, $pageVars["route"]["extraParams"]) ;

        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel(array());
        $taskRunners = $tasks[$task] ;
        $this->content["result"] = array() ;
        foreach ($taskRunners as $taskRunnerKey => $params) {
            var_dump("epa:", "davdfe") ;
            $taskRunner = array_keys($params);
            $taskRunner = $taskRunner[0];
            //var_dump($taskRunner) ;
            switch ($taskRunner) {
                case "cleopatra" :
                    if (is_string($params[$taskRunner])) { $af = $params[$taskRunner] ; }
                    else if (is_array($params[$taskRunner]) && isset($params[$taskRunner]["af"])) { $af = $params[$taskRunner]["af"] ; }
                    else if (is_array($params[$taskRunner]) && isset($params[$taskRunner][0])) { $af = $params[$taskRunner][0] ; }
                    else { $af = $params[$taskRunner][0] ; }
                    $logging->log("Cleopatra Task Runner", "Task") ;
                    $this->params["autopilot-file"] = $af ;
                    $auto = new \Controller\Autopilot();

                    $ep = array_merge( $this->params, $pageVars["route"]["extraParams"]) ;
                    var_dump("ep:", $ep) ;

                    $route = array("control" => "Autopilot", "action" => "install", "extraParams" => $ep) ;
                    $emptyPageVars = array("messages"=>array(), "route"=>$route);
                    $ax = $auto->execute($emptyPageVars);

                    if (isset($ax["pageVars"]["messages"])) { $this->content["result"][] = $ax["pageVars"]["messages"] ; }
                    else if (isset($ax["pageVars"]["autoExec"])) { $this->content["result"][] = $ax["pageVars"]["autoExec"] ; }
                    else { $this->content["result"][] = "Unreadable Output" ;}
                    break ;
                case "dapperstrano" :
                    if (is_string($params[$taskRunner])) { $af = $params[$taskRunner] ; }
                    else if (is_array($params[$taskRunner]) && isset($params[$taskRunner]["af"])) { $af = $params[$taskRunner]["af"] ; }
                    else if (is_array($params[$taskRunner]) && isset($params[$taskRunner][0])) { $af = $params[$taskRunner][0] ; }
                    else { $af = $params[$taskRunner]["af"] ; }
                    $logging->log("Dapperstrano Task Runner","Task") ;
                    exec(DAPPCOMM.'autopilot execute --af="'.$af.'" '.$sourceParams, $this->content["result"]) ;
                    break ;
                case "log" :
                    $logging->log("Logging Task Runner","Task") ;
                    if (is_string($params[$taskRunner])) { $log = $params[$taskRunner] ; }
                    else if (is_array($params[$taskRunner]) && isset($params[$taskRunner]["log"])) { $log = $params[$taskRunner]["log"] ; }
                    else if (is_array($params[$taskRunner]) && isset($params[$taskRunner][0])) { $log = $params[$taskRunner][0] ; }
                    else { $log = "No Log Provided" ; }
                    $this->content["result"][] = "Logging Task: $log"; ;
                    $logging->log($log,"Task") ;
                    break ;
                default :
                    $msg ="Undefined Task Runner $taskRunner requested" ;
                    $this->content["result"][] = $msg ;
                    $logging->log($msg, "Task") ;
                    break ;
            } }
        return array ("type"=>"view", "view"=>"Task", "pageVars"=>$this->content);
    }

    protected static function getTaskfileTasks($pageVars, $taskFile = "Taskfile") {
        if (file_exists($taskFile)) {
            try {
                require_once ($taskFile) ; }
            catch (\Exception $e) {
                echo "Error loading Taskfile $taskFile, error $e\n" ; } }
        else { return array() ; }
        $taskObject = new \Model\Taskfile(self::formatParams(array_merge(array("silent"=>true),$pageVars))) ;
        $tftasks = $taskObject->getTasks() ;
        return $tftasks ;
    }

    private static function formatParams($params) {
        $newParams = array();
        foreach($params as $origParamKey => $origParamVal) {
            $newParams[] = '--'.$origParamKey.'='.$origParamVal ; }
        $newParams[] = '--yes' ;
        $newParams[] = "--hide-title=yes";
        $newParams[] = "--hide-completion=yes";
        return $newParams ;
    }

}