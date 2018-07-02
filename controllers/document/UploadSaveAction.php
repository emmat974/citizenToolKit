<?php
class UploadSaveAction extends CAction {
	
    //$folder is the $type of the element
	public function run($dir,$folder=null,$ownerId=null,$input, $contentKey=false, $docType=false, $rename=false, $subDir=null, $keySurvey=null) {
		
        $res = array('result'=>false, 'msg'=>Yii::t("document","Something went wrong with your upload!"));
        if (Person::logguedAndValid()) 
        {
            if(strtolower($_SERVER['REQUEST_METHOD']) != 'post') {
                $res = array('result'=>false,'error'=>Yii::t("document","Error! Wrong HTTP method!"));
            }
            //{"result":false,"msg":"Le chargement du document ne s'est pas deroul\u00e9 correctement",
            //"file":{ "qqfile":{"name":"compo.jpg","type":"","tmp_name":"","error":6,"size":0}}}
            if(array_key_exists($input,$_FILES) && $_FILES[$input]['error'] == 0 ) {
                $file = $_FILES[$input];
            } else {
                error_log("WATCH OUT ! - ERROR WHEN UPLOADING A FILE ! CHECK IF IT'S NOT AN ATTACK");
                $res = array('result'=>false,'msg'=>Yii::t("document","Something went wrong with your upload!"));
            }
            $res['file'] = @$file;   

            $res = Document::checkFileRequirements($file, $dir, $folder, $ownerId, $input, @$contentKey, @$docType, @$subDir, @$keySurvey);
            if ($res["result"]) {
                $res = Document::uploadDocument($file, $res["uploadDir"],$input,$rename);
                if ($res["result"]) {
                    $res = array('resultUpload'=>true,
                                "success"=>true,
                                'name'=>$res["name"],
                                //'dir'=> $res["uploadDir"],
                                'size'=> (int)filesize ($res["uploadDir"].$res["name"]) );
                }
            }
            $res2 = array();
            
            if( $res["resultUpload"] ){
            //error_log("resultUpload xxxxxxxxxxxxxxxx");
                if($contentKey==false){
                    if(@$_POST["contentKey"]) $contentKey=$_POST["contentKey"];
                    else $contentKey=Document::IMG_PROFIL;
                }
                $subFolder="";
                if(@$_POST["formOrigin"])
                    $subFolder="/".$_POST["formOrigin"];
                if($contentKey==Document::IMG_SLIDER)
                    $subFolder="/".Document::GENERATED_ALBUM_FOLDER;
                if(@$docType && $docType==Document::DOC_TYPE_FILE){
                    $subFolder="/".Document::GENERATED_FILE_FOLDER;
                    if($contentKey!="survey")
                        $contentKey="";
                    else{
                        $subFolder.="/".$contentKey."/".$keySurvey."/".$subDir;
                    }
                }
                $params = array(
                    "id" => $ownerId,
                    "type" => $folder,
                    "folder" => $folder."/".$ownerId.$subFolder,
                    "moduleId" => "communecter",
                    "name" => $res["name"],
                    "size" => (int) $res['size'],
                    "contentKey" => $contentKey,
                    "author" => Yii::app()->session["userId"]
                );
                if(@$docType && $docType==Document::DOC_TYPE_FILE)
                    $params["doctype"]=$docType;
                if(@$docType && $docType==Document::DOC_TYPE_FILE)
                    $params["doctype"]=$docType;
                if(@$keySurvey && @$subDir)
                    $params["keySurvey"]=$subDir;
                if(@$_POST["parentType"])
                    $params["parentType"] = $folder;
                if(@$_POST["parentId"])
                    $params["parentId"] = $ownerId;           
                if(@$_POST["formOrigin"])
                    $params["formOrigin"] = $_POST["formOrigin"];
                if(@$_POST["cropX"] || @$_POST["cropY"] || @$_POST["cropW"] || @$_POST["cropH"]){
                    $params["crop"]=array("cropX" => $_POST["cropX"],"cropY" => $_POST["cropY"],"cropW" => $_POST["cropW"],"cropH" => $_POST["cropH"]);
                }
                $res2 = Document::save($params);
            }

        } else 
            $res2 = array("result" => false, "msg" => Yii::t("common","Please Log in order to update document !"));
            
        
        $res = array_merge($res,$res2 );
        return Rest::json($res);
	}

}