<?php

	$inData = getRequestInfo();
	$username = $inData["username"];
	$email = $inData["email"];
	$password = $inData["password"];

	$conn = new mysqli("localhost", "EventApp", "COP4710", "COP4710"); 	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		$stmt = $conn->prepare("SELECT * FROM Users WHERE username=?");
		$stmt->bind_param("s", $username);
        $stmt->execute()

		$result = $stmt->get_result();
		if ($row = $result->fetch_assoc())
        {
            returnWithError("Username already in use");
        }

		else
		{
			$stmt2 = $conn->prepare("INSERT INTO Users (username, email, password) VALUES (?,?,?)");
            $stmt2->bind_param("sss", $username, $email, $password);
            $stmt2->execute();
            $stmt2->close();

			$stmt3 = $conn->prepare("SELECT * FROM Users WHERE username=?");
            $stmt3->bind_param("s", $username);
            $stmt3->execute();

			$result2 = $stmt3->get_result();

			if ($row2 = $result2->fetch_assoc())
            {
                returnWithInfo($row2['UID'] $row2['username'], $row2['email']);
            }

            else
            {
                returnWithError("Error");
            }

            $stmt3->close();
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
