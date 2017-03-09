<?php

class Zone {

	const COLLECTION = "zones";
	//const CONTROLLER = "zone";
	const COLOR = "#E6304C";
	const ICON = "fa-university";

	public static $dataBinding = array(
	   /* "name" => array("name" => "name", "rules" => array("required")),
	    "alternateName" => array("name" => "alternateName", "rules" => array("required")),
	    "insee" => array("name" => "insee", "rules" => array("required")),
	    "country" => array("name" => "birthDate", "rules" => array("required")),
	    "geo" => array("name" => "geo", "rules" => array("required","geoValid")),
	    "geoPosition" => array("name" => "geoPosition", "rules" => array("required","geoPositionValid")),
	    "geoShape" => array("name" => "geoShape"),
	 	"postalCodes" => array("name" => "postalCodes"),
	    "regionName" => array("name" => "regionName"),
	    "region" => array("name" => "region"),
	    "depName" => array("name" => "depName"),
	    "dep" => array("name" => "dep"),
	    "osmID" => array("name" => "osmID"),
	    "wikidataID" => array("name" => "wikidataID"),
	    "modified" => array("name" => "modified"),
	    "updated" => array("name" => "updated"),
	    "creator" => array("name" => "creator"),
	    "created" => array("name" => "created"),
	    "new" => array("name" => "new")*/
	);

	/* Retourne des infos sur la commune dans la collection cities" */
	public static function getWhere($params, $fields=null, $limit=20) {
	  	$zones =PHDB::findAndSort( self::COLLECTION,$params, array(), $limit, $fields);
	  	return $zones;
	}

	public static function getDetailById($id){
		$where = array("_id"=>new MongoId($id));
		$zone = PHDB::findOne(self::COLLECTION, $where);

		$city =  City::getByInsee($zone["insee"]);
		//$zone["cityName"] = $city["name"];
		$zone["depName"] = $city["depName"];
		$zone["regionName"] = $city["regionName"];
		return $zone;
	}


	public static function createLevel($name, $countryCode, $level, $parentKey){
		$zoneNominatim = array() ;
		$zone = array();

		if($level == "1")
			$zoneNominatim = json_decode(SIG::getGeoByAddressNominatim(null,null, null, $countryCode, true, true, $name), true);
		else if($level == "2" || $level == "3" || $level == "4")
			$zoneNominatim = json_decode(SIG::getGeoByAddressNominatim(null,null, null, $countryCode, true, true, $name, true), true);
		
		if(!empty($zoneNominatim)){
			$zone["name"] = $name;
			$zone["countryCode"] = $countryCode;
			$zone["level"] = $level;
			if($level != "1"){
				
				$zone["parentKey"] = $parentKey;
			}
			$zone["geo"] = SIG::getFormatGeo($zoneNominatim[0]["lat"], $zoneNominatim[0]["lon"]);
			$zone["geoPosition"] = SIG::getFormatGeoPosition($zoneNominatim[0]["lat"], $zoneNominatim[0]["lon"]);
			$zone["geoShape"] = $zoneNominatim[0]["geojson"];
			$zone["osmID"] = $zoneNominatim[0]["osm_id"];
			if(!empty($zoneNominatim[0]["extratags"]["wikidata"]))
				$zone["wikidataID"] = $zoneNominatim[0]["extratags"]["wikidata"];
		}
		return $zone;
	}

	public static function save($zone){
		$res = array( 	"result" => false, 
						"error"=>"400",
						"msg" => "error" );
		if(!empty($zone)){
			PHDB::insert(self::COLLECTION, $zone );
			$res = array( 	"result" => true, 
							"msg" => "création Country" );
		}
	}


	public static function createKey($zone){
		$key = "";
		if($zone["level"] != "1"){
			$country = self::getCountryByCountryCode($zone["countryCode"]);
			$key .= (String)$country["_id"];

			if($zone["level"] == "2"){
				$key .= "@".(String)$zone["_id"] ;
			}
			else{
				$level2 =  ( ( empty($zone["level2"]) ) ? null : self::getZoneById($zone["level2"]) ) ;
				$key .= "@".( ( empty($level2["_id"]) ) ? "" : (String)$level2["_id"] );

				if($zone["level"] == "3"){
					$key .= "@".(String)$zone["_id"] ;
				}
				else{
					$level3 =  ( ( empty($zone["level3"]) ) ? null : self::getZoneById($zone["level3"]) ) ;
					$key .= "@".( ( empty($level3["_id"]) ) ? "" : (String)$level3["_id"] );

					if($zone["level"] == "4"){
						$key .= "@".(String)$zone["_id"] ;
					}else{
						$level4 =  ( ( empty($zone["level4"]) ) ? null : self::getZoneById($zone["level4"]) ) ;
						$key .= "@".( ( empty($level4["_id"]) ) ? "" : (String)$level4["_id"] );
					}
				}
			}
		}
	}


	public static function getCountryByCountryCode($countryCode){
		$where = array(	"country"=> $countryCode,
						"level" => "1");
		$country = PHDB::findOne(self::COLLECTION, $where);
		return $country;
	}


	public static function getAreaAdministrative($countryCode, $level, $idZone = null, $idInCountry = null, $name = null){
		$where = array(	"country"=> $countryCode,
						"level" => $level);

		$zone = array();
		if(!empty($idInCountry) || !empty($name) ){

			if(!empty($idInCountry))
				$where["idInCountry"] = $idInCountry ;

			if(!empty($name))
				$where["name"] = $name ;

			$zone = PHDB::findOne(self::COLLECTION, $where);
		}
		
		return $zone;
	}

}
?>