<?php

	$inData = getRequestInfo();
	$userID = $inData["userID"];
	
	$searchResults = "";
	$searchCount = 0;

	$conn = new mysqli("localhost", "EventApp", "COP4710", "COP4710"); 	
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("SELECT * FROM ");
		$stmt->bind_param("ii", $userID, $userID);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc())
		{
			if( $searchCount > 0 )
			{
				$searchResults .= ",";
			}
			$searchCount++;
			$searchResults .= '{
				"eventID":' . $row["eventID"] . ', 
				"name":"' . $row["name"] . '", 
				"category":"' . $row["category"] . '", 
				"description":"' . $row["description"] . '", 
				"contactPhone":"' . $row["contactPhone"] . '", 
				"contactEmail":"' . $row["contactEmail"] . '", 
				"timestamp":"' . $row["timestamp"] . '", 
				"locID":"' . $row["locID"] . '"
			}';
		}
		
		if( $searchCount == 0 )
		{
			returnWithError( "No Records Found" );
		}
		else
		{
			returnWithInfo( $searchResults );
		}
		
		$stmt->close();
		$conn->close();
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $searchResults )
	{
		$retValue = '{"results":[' . $searchResults . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>