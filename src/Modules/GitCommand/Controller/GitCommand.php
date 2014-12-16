<?php

Namespace Controller ;

class GitCommand extends Base {

    public function execute($pageVars) {

        $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars) ;
        // if we don't have an object, its an array of errors
        if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
        $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
        if ( is_array($isDefaultAction) ) { return $isDefaultAction; }

        $action = $pageVars["route"]["action"];

        if (in_array($action, array("create-branch", "delete-branch", "ensure-branch", "add", "commit", "push", "pull") )) {
            $this->content["result"] = $thisModel->askAction($action);
            $this->content["appName"] = $thisModel->programNameInstaller ;
            return array ("type"=>"view", "view"=>"boxify", "pageVars"=>$this->content); }

        $this->content["messages"][] = "Invalid GitCommand Action";
        return array ("type"=>"control", "control"=>"index", "pageVars"=>$this->content);
    }

}