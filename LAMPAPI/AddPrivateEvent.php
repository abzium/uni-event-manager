<?php
	$inData = getRequestInfo();
	
	$name = $inData["name"];
	$category = $inData["category"];
	$description = $inData["description"];
	$contactPhone = $inData["contactPhone"];
	$contactEmail = $inData["contactEmail"];
	$timestamp = $inData["timestamp"];
	$adminID = $inData["adminID"];
    $uniID = $inData["uniID"];

	$conn = new mysqli("localhost", "EventApp", "COP4710", "COP4710");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("INSERT INTO Events (name, category, description, contactPhone, contactEmail, timestamp) VALUES (?, ?, ?, ?, ?, ?);");
		$stmt->bind_param("ssssss", $name, $category, $description, $contactPhone, $contactEmail, $timestamp );
		$stmt->execute();
		$stmt->close();

		// get eventID
		$stmt2 = $conn->prepare("SELECT LAST_INSERT_ID() AS eventID;");
		$stmt2->execute();
		$result = $stmt2->get_result();
		$row = $result->fetch_assoc();
		$eventID = $row['eventID'];
		$stmt2->close();

		// add to private events
		$stmt3 = $conn->prepare("INSERT INTO Private_Events (eventID, adminID, uniID) VALUES (?, ?, ?);");
		$stmt3->bind_param("iii", $eventID, $adminID, $uniID);
		$stmt3->execute();
		$stmt3->close();

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