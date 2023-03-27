<?php
    // incoming: adminID, name, domain
	$inData = getRequestInfo();
	$adminID = $inData["adminID"];
	$name = $inData["name"];
	$domain = $inData["domain"];

	$conn = new mysqli("localhost", "EventApp", "COP4710", "COP4710"); 	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
        // Check if rso name already exists
		$stmt = $conn->prepare("SELECT * FROM RSOs WHERE name=?");
		$stmt->bind_param("s", $name);
        $stmt->execute();

		$result = $stmt->get_result();
		if ($row = $result->fetch_assoc())
        {
            returnWithError("RSO name already in use");
        }

		else
		{
			// Insert RSO into RSOs table
            $stmt2 = $conn->prepare("INSERT INTO RSOs (adminID, name, domain) VALUES (?,?,?)");
            $stmt2->bind_param("iss", $adminID, $name, $domain);
            $stmt2->execute();
            $stmt2->close();

            // Retrieve rsoID of the new RSO
			$stmt3 = $conn->prepare("SELECT * FROM RSOs WHERE name=?");
            $stmt3->bind_param("s", $name);
            $stmt3->execute();

			$result2 = $stmt3->get_result();

			if ($row2 = $result2->fetch_assoc())
            {
                $rsoID = $row2['rsoID'];

                // Insert rsoID and userID into RSO_Students
                $stmt4 = $conn->prepare("INSERT INTO RSO_Students (rsoID, userID) VALUES (?,?)");
                $stmt4->bind_param("ii", $rsoID, $adminID);
                $stmt4->execute();
                $stmt4->close();

                returnWithInfo($row2['rsoID'], $row2['adminID'], $row2['name'], $row2['status'], $row2['domain']);
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
		$retValue = '{"rsoID":0,"adminID":0,"name":"","status":"","domain":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $rsoID, $adminID, $name, $status, $domain )
	{
		$retValue = '{"rsoID":' . $rsoID . ',"adminID":' . $adminID . ',"name":"' . $name . '","status":"' . $status . '","domain":"' . $domain . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>