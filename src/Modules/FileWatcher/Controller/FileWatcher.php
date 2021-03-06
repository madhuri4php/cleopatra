<?php

Namespace Controller ;

class FileWatcher extends Base {

    public function execute($pageVars) {

        $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars) ;
        // if we don't have an object, its an array of errors
        if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
        $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
        if ( is_array($isDefaultAction) ) { return $isDefaultAction; }

        $action = $pageVars["route"]["action"];
        $this->content["route"] = $pageVars["route"] ;

        if ($action=="path") {
            $this->content["result"] = $thisModel->askWhetherToFileWatcher();
            return array ("type"=>"view", "view"=>"FileWatcher", "pageVars"=>$this->content); }

        $this->content["messages"][] = "Invalid FileWatcher Action";
        return array ("type"=>"control", "control"=>"index", "pageVars"=>$this->content);

    }

}