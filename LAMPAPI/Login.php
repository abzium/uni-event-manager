<?php

	$inData = getRequestInfo();
	
	$conn = new mysqli("localhost", "EventApp", "COP4710", "COP4710"); 	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		$stmt = $conn->prepare("SELECT UID, username, email FROM Users WHERE username=? AND password=?");
		$stmt->bind_param("ss", $inData["username"], $inData["password"]);
		$stmt->execute();
		$result = $stmt->get_result();

		if( $row = $result->fetch_assoc()  )
		{
			returnWithInfo($row['UID'], $row['username'], $row['email']);
		}
		else
		{
			returnWithError("No Records Found");
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
		$retValue = '{"UID":0,"username":"","email":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $id, $username, $email )
	{
		$retValue = '{"UID":' . $id . ',"username":"' . $username . '","email":"' . $email . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
