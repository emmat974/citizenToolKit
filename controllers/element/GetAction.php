<?php

class GetAction extends CAction {
/**
* Get element by Id and type
*/

  public function run($type,$id, $update=null) { 
    if (isset(Yii::app()->session["userId"])) {
      try {
        $res = array("result" => true, "map" => Element::getByTypeAndId($type,$id, null, $update) );
      } catch (CTKException $e) {
        $res = array("result"=>false, "msg"=>$e->getMessage());
      }
    } else {
      $res = array("result"=>false, "msg"=>"Please login first");
    }
    Rest::json($res);
  }

}

?>