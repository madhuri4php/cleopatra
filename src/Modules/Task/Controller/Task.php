<?php

Namespace Controller ;

class Task extends Base {

    public function execute($pageVars) {

        $action = $pageVars["route"]["action"];

        $taskFileExecutor = $this->getTaskfileTaskForAction($pageVars, $action);
        if (!is_null($taskFileExecutor)) {
            return $taskFileExecutor->executeTFTask($pageVars, $action) ; }

        $otherModuleExecutor = $this->getModuleExecutorForAction($action);
        if (!is_null($otherModuleExecutor)) {
            return $otherModuleExecutor->executeTask($pageVars) ; }

        if ($action=="help") {
            $helpModel = new \Model\Help();
            $this->content["helpData"] = $helpModel->getHelpData($pageVars["route"]["control"]);
            return array ("type"=>"view", "view"=>"help", "pageVars"=>$this->content); }

        if (in_array($action, array("list") )) {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "Listing") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $this->content["result"] = $thisModel->askAction($action);
            $this->content["appName"] = $thisModel->programNameInstaller ;
            return array ("type"=>"view", "view"=>"TaskList", "pageVars"=>$this->content); }

    }

    protected function getTaskfileTaskForAction($pageVars, $action) {
        $tftasks = $this->getTaskfileTasks($pageVars);
        if (in_array($action, $tftasks)) { return new \Controller\TaskExecutor(); }
        return null ;
    }

    protected function getTaskfileTasks($pageVars, $taskFile = "Taskfile") {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($pageVars);
        if (file_exists($taskFile)) {
            try {
                require_once ($taskFile) ; }
            catch (\Exception $e) {
                echo "dave" ;  } }
        else {
            echo "dave 2" ; }
        $taskObject = new \Model\Taskfile($pageVars) ;
        $taskObject->params["silent"] = true ;
        $tftasks = $taskObject->getTasks() ;
        return $tftasks ;
    }

    private function formatParams($params) {
        $newParams = array();
        foreach($params as $origParamKey => $origParamVal) {
            $newParams[] = '--'.$origParamKey.'='.$origParamVal ; }
        $newParams[] = '--yes' ;
        $newParams[] = "--hide-title=yes";
        $newParams[] = "--hide-completion=yes";
        return $newParams ;
    }

    protected function getModuleExecutorForAction($action) {
        $controllers = \Core\AutoLoader::getAllControllers() ;
        foreach ($controllers as $controller) {
            if (method_exists($controller, "executeTask"))
                $info = \Core\AutoLoader::getSingleInfoObject(substr(get_class($controller), 11)) ;
            $myTaskRoutes = (isset($info) && method_exists($info, "taskActions")) ? $info->taskActions() : array() ;
            if (in_array($action, $myTaskRoutes)) {
                return $controller ; } }
        return null ;
    }

}