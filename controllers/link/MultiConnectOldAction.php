<?php

class MultiConnectAction extends CAction
{
	 /**
	 * TODO Clement : La PHPDOC
	 */
    public function run() {

    	assert('!empty($_POST["childs"])'); //The childs are mandatory');
	    //assert('!empty($_POST["childType"])'); //The child type is mandatory');
	    assert('!empty($_POST["parentId"])'); //The parent id is mandatory');
	    assert('!empty($_POST["parentType"])'); //The parent type is mandatory');

	    $result = array("result"=>false, "msg"=>Yii::t("common", "Incorrect request"));
		
		if ( ! Person::logguedAndValid() ) {
			return array("result"=>false, "msg"=>Yii::t("common", "You are not loggued or do not have acces to this feature "));
		}
	
		$parentId = $_POST["parentId"];
    	$parentType = $_POST["parentType"];
    	//$isConnectingAdmin = @$_POST["connectType"];
		$newMembers = array();
		$msg=false;
		$finalResult = false;
		$onlyOrganization=true;
		foreach($_POST["childs"] as $key => $contact){
			if(@$contact["childId"] != $parentId ){
				$roles="";
			    $child = array(
					"childId" => @$contact["childId"],
			    	"childType" => @$contact["childType"] == "people" ? "citoyens" : @$contact["childType"],
			    	"childName" => @$contact["childName"],
		            "childEmail" => @$contact["childEmail"],
			    );
			    if(@$contact["roles"])
			    	$roles=$contact["roles"];
		    	if($child["childType"]==Person::COLLECTION)
		    		$onlyOrganization=false;    	
		    	$isConnectingAdmin= (@$contact["connectType"]=="admin") ? true : false;
		    	
		    	$res = Link::connectParentToChild($parentId, $parentType, $child, $isConnectingAdmin, Yii::app()->session["userId"], $roles);
		    	if($res["result"] == true){
			    	if($msg != 2)
			    		$msg=1;
					$newMember = $res["newElement"];
			    	$newMember["childType"] = $res["newElementType"];
			    	array_push($newMembers, $newMember);
			    	$finalResult=true; 
				} else {
					if($msg==1){
						$msg=2;
					}else if($msg != 2){
						$msg=false;
					}
				}
			}
	 	}
	 	if($finalResult == true){
		 	if($msg==1)
		 		$msg = Yii::t("common","New member(s) have been succesfully added");
		 	else
		 		$msg = Yii::t("common","New member(s) have been succesfully added except those already in the community");		 		
	 		$result = array("result"=>true, "msg" => $msg,"newMembers" => $newMembers, "onlyOrganization"=>$onlyOrganization);
		}else $result = $res;
		
		Rest::json($result);
    }

}