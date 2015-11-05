<?php

	$con = mysql_connect('localhost:3306','lsusr_11369','NeUHbWWThcYQeaZG'); 
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}
	
	$surveyID = (int)$_GET["surveyID"];
	$groupID = (int) $_GET["groupID"];
	$questionID = (int)$_GET["questionID"];
	$searchString = mysql_real_escape_string($_GET["searchString"]);
	

	mysql_select_db("lsusr_11369");  //change "db" to the name of your actual database.
	
	$query = "SELECT ".$surveyID."X".$groupID."X".$questionID." AS ANSWER FROM lsusr_11369.survey_".$surveyID." where ".$surveyID."X".$groupID."X".$questionID." like '".$searchString."%'";
	
	$returned = mysql_query($query);
	
	
	if (!$returned)
	{
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}

	
	else
	{
		$jsonReturn = "";
		while ($row = mysql_fetch_assoc($returned)) 
		{
			$jsonReturn = $jsonReturn . $row['ANSWER'] . ",";
		}
		echo json_encode($jsonReturn);
		
	}
	mysql_free_result($returned);
    mysql_close($con); 
?>

