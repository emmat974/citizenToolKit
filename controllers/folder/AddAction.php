<?php
class AddAction extends CAction
{
    public function run()
    {
        $res = array( "result" => false , "msg" => Yii::t("common","Something went wrong!") );
        if( !Person::logguedAndValid() )
            return array("result"=>false, "msg"=>Yii::t("common","Please Login First") );
        else{	
			try {
				$res = Folder::create( @$_POST['id'], @$_POST['type'], @$_POST['name']);
			} catch (CTKException $e) {
				$res = array( "result" => false , "msg" => $e->getMessage() );
			}
		}

		return Rest::json($res);
    }
}