<?php

	$inData = getRequestInfo();
	$firstName = $inData["firstName"];
	$lastName = $inData["lastName"];
	$email = $inData["email"];
	$password = $inData["password"];

	$conn = new mysqli("localhost", "EventApp", "COP4710", "COP4710"); 	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		$stmt = $conn->prepare("SELECT * FROM Users WHERE email=?");
		$stmt->bind_param("s", $email);
        $stmt->execute();

		$result = $stmt->get_result();
		if ($row = $result->fetch_assoc())
        {
            returnWithError("Email already in use");
        }

		else
		{
			$stmt2 = $conn->prepare("INSERT INTO Users (firstName, lastName, email, password) VALUES (?,?,?,?)");
            $stmt2->bind_param("ssss", $firstName, $lastName, $email, $password);
            $stmt2->execute();
            $stmt2->close();

			$stmt3 = $conn->prepare("SELECT * FROM Users WHERE email=?");
            $stmt3->bind_param("s", $email);
            $stmt3->execute();

			$result2 = $stmt3->get_result();

			if ($row2 = $result2->fetch_assoc())
            {
                returnWithInfo($row2['userID'], $row2['firstName'], $row2['lastName'], $row2['userLevel']);
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
		$retValue = '{"userID":0,"firstName":"","lastName":"","userLevel":0,"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $id, $firstName, $lastName, $userLevel )
	{
		$retValue = '{"userID":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","userLevel":' . $userLevel . ',"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
