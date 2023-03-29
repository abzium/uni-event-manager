<?php

	$inData = getRequestInfo();
	$firstName = $inData["firstName"];
	$lastName = $inData["lastName"];
	$email = $inData["email"];
	$domain = $inData["domain"];
	$password = $inData["password"];

	$conn = new mysqli("localhost", "EventApp", "COP4710", "COP4710"); 	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		$stmt = $conn->prepare("SELECT * FROM Users WHERE email=?;");
		$stmt->bind_param("s", $email);
        $stmt->execute();

		$result = $stmt->get_result();
		if ($row = $result->fetch_assoc())
        {
            returnWithError("Email already in use");
        }

		else
		{
			// find university
			$uniQuery = $conn->prepare("SELECT * FROM Universities WHERE domain=?;");
			$uniQuery->bind_param("s", $domain);
			$uniQuery->execute();

			$uniResult = $uniQuery->get_result();
			if ($uniRow = $uniResult->fetch_assoc())
			{
				$uniID = $uniRow['uniID'];
			}
			else
			{
				returnWithError("University doesn't exist.");
			}
			$uniQuery->close();
			
			$stmt2 = $conn->prepare("INSERT INTO Users (firstName, lastName, email, password, uniID) VALUES (?,?,?,?,?);");
            $stmt2->bind_param("sssss", $firstName, $lastName, $email, $password, $uniID);
            $stmt2->execute();
            $stmt2->close();

			$stmt3 = $conn->prepare("SELECT * FROM Users WHERE email=?;");
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
