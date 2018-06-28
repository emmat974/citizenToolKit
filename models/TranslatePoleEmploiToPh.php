<?php 

class TranslatePoleEmploiToPh {

	public static $mapping_offres = array(

		"ROME_PROFESSION_CARD_NAME" => "name",
		"LONGITUDE" => "geo.longitude",
		"LATITUDE" => "geo.latitude",
		"CONTRACT_TYPE_NAME" => "type",
		// "ROME_PROFESSION_CARD_CODE" => 
		"ROME_PROFESSION_NAME" => "shortDescription",
		"ACTIVITY_NAME" => "tags.0",
		"CONTRACT_TYPE_CODE" => "tags.1",
		"QUALIFICATION_NAME" => "tags.2",
		"DEGREE_TYPE_NAME_1" => "info1",
		"geoP" 	=> array("valueOf" => array(
									"@type" 			=> "GeoCoordinates", 
									"latitude" 			=> array("valueOf" => "LATITUDE"),
									"longitude" 		=> array("valueOf" => "LONGITUDE")
				 					)),
	);

}

?>