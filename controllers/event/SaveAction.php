<?php
class SaveAction extends CAction
{
    public function run()
    {
        $controller=$this->getController();
        
        //TODO check by key
        if(!Event::checkExistingEvents($_POST))
        { 
          	try {
            	$res = Event::saveEvent($_POST);
            } catch (CTKException $e) {
            	$res = array("result"=>false, "msg"=>$e->getMessage());
            }

          	Rest::json($res);
        } else
            Rest::json(array("result"=>false, "msg"=>"Cette Evenement existe déjà."));
       
        exit;
    }
}