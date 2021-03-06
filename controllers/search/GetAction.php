<?php
class GetAction extends CAction {

	public function run($search = "", $format = null, $limit=50, $index=0, $tags = null, $multiTags=null , $key = null, $insee = null) {
	$controller=$this->getController();
		// Get format
		/*if( $format == Translate::FORMAT_SCHEMA)
			$bindMap = (empty($id) ? TranslateSchema::$dataBinding_allPerson : TranslateSchema::$dataBinding_person);
		else if( $format == Translate::FORMAT_PLP )
			$bindMap = TranslatePlp::$dataBinding_person;
		else if( $format == Translate::FORMAT_AS )
			$bindMap = TranslateActivityStream::$dataBinding_person;
		else if( $format == Translate::FORMAT_KML)
			$bindMap = (empty($id) ? TranslateKml::$dataBinding_allPerson : TranslateKml::$dataBinding_person);
		else if( $format == Translate::FORMAT_GEOJSON)
			$bindMap = (empty($id) ? TranslateGeoJson::$dataBinding_allPerson : TranslateGeoJson::$dataBinding_person);
		else */
			//$bindMap = (empty($id) ? TranslateCommunecter::$dataBinding_allPerson : TranslateCommunecter::$dataBinding_person);

		$result = Api::getDataBySearch($search, $format, $tags , $index,$limit);

		/*if ($format == Translate::FORMAT_KML) {
			$strucKml = News::getStrucKml();    
			Rest::xml($result, $strucKml,$format);
		} else */
			Rest::json($result);

		Yii::app()->end();
	}
}