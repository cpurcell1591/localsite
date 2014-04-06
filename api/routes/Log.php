<?php
require_once("./library/Helpers.php");
require_once("./library/DB.php");
   

Class Log {
  private $checkinDb;
  public function __constructor() {
    $this->checkinDb = new DB("checkin");
  }

  public function getAll($params) {
	//build query based on optional parameters
   	$sql = "SELECT * FROM `$orgId` WHERE `meetingId` = :meetingId";
	$sql_tot = "SELECT COUNT(*) as `total` FROM `$orgId` WHERE `meetingId` = :meetingId";
   
	//if the user specifies year
   	if(array_key_exists("year", $params)) { 
		$year = $params["year"]; 
		$res["year"] = $params["year"];
		$sql .= " AND YEAR(`timestamp`) = :year";
		$sql_tot .= " AND YEAR(`timestamp`) = :year";
	}
	//if the user specifies month
   	if(array_key_exists("month", $params)) { 
		$month = $params["month"]; 
       	$res["month"] = $params["month"];
		$sql .= " AND MONTHNAME(`timestamp`) = :month";
		$sql_tot .= " AND MONTHNAME(`timestamp`) = :month";
   	}
	//if the user specifies day
	if(array_key_exists("day", $params)) { 
		$day = $params["day"]; 
       	$res["day"] = $params["day"];
		$sql .= " AND DAY(`timestamp`) = :day";
		$sql_tot .= " AND DAY(`timestamp`) = :day";
   	}
	//page and size specifications
   	if(array_key_exists("page", $params) && array_key_exists("size", $params)){ 
		$page = $params["page"]; 
		$res["page"] = $params["page"];
   		$size = $params["size"]; 
		$res["size"] = $params["size"];
		$start = ($page - 1) * $size;
		$end = ($page * $size) - 1;
		$sql .= " ORDER BY timestamp, DESC LIMIT :start, :end";
		$sql_tot .= " ORDER BY timestamp, DESC LIMIT :start, :end";
   	}
    $hits = $this->checkinDb->fetchAll($sql, array("meetingId"=>$meetingId), "COLUMN");
	$total = $this->checkinDb->fetchAll($sql_tot, array("meetingId"=>$meetingId), "COLUMN");
	$res["total"] = $total;
    $res["hits"] = $hits;
    return $res;
  }

  public function getOverview($params){
	$orgId = Helpers::id2Int($params["orgId"]);
    $res["orgId"] = $orgId;
    $meetingId = $params["meetingId"];
    $res["meetingId"] = $meetingId;
	//grab the overview of all attendance counts and group by year, month, day
	$sql = "SELECT YEAR(`timestamp`) as `year`, MONTHNAME(`timestamp`) as `month`, DAYOFMONTH(`timestamp`) as `day`, COUNT(*) as `total` FROM `$orgId` WHERE `meetingId` = :meetingId GROUP BY `year`, `month`, `day`";
	}
	$hits = $this->checkinDb->fetchAll($sql, array("meetingId"=>$meetingId), "COLUMN");
	//order data for display in the pivot table
	$output = array();
	foreach($hits as $row) {
  		$year = $row["year"]
  		$month = $row["month"]
  		$day = $row["day"]
  		$total = $row["total"]
  		if(!isset($hits[$year])) { $output[$year] = array(); }
  		if(!isset($hits[$year][$month])) { $output[$year][$month] = array(); }
  		$output[$year][$month][$day] = $total;
    $res["output"] = $output;
    return $res;
}

?>
