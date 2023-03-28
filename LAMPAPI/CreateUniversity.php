<?php
	$inData = getRequestInfo();
	
	$name = $inData["name"];
	$description = $inData["description"];
	$domain = $inData["domain"];
	$numStudents = $inData["numStudents"];

	$conn = new mysqli("localhost", "EventApp", "COP4710", "COP4710");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("INSERT INTO Universities (name, description, domain, numStudents) VALUES (?, ?, ?, ?);");
		$stmt->bind_param("ssss", $name, $description, $domain, $numStudents);
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