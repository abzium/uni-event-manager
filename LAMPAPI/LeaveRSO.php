<?php
	$inData = getRequestInfo();
	
	$rsoID = $inData["rsoID"];
	$userID = $inData["userID"];

	$conn = new mysqli("localhost", "EventApp", "COP4710", "COP4710");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("DELETE FROM RSO_Students WHERE rsoID=? AND userID=?;");
		$stmt->bind_param("ii", $rsoID, $userID);
		$stmt->execute();
		$stmt->close();

		$conn->close();
		returnWithError("");
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
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>