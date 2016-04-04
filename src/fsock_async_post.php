<?php

// Original code from http://stackoverflow.com/questions/962915/how-do-i-make-an-asynchronous-get-request-in-php
function utils_fsock_async_post($argIp, $argUrl, $argParameters, $argConnectionTimeout=null, $argCommunicationTimeout=null)
{
	try {
		$lErrNo=0;
		$lErrStr='';
	
		$lParts=parse_url($argUrl);

		$lDefaultParts=array(
	    	'scheme'=>null,
	    	'host'=>null,
	    	'port'=>null,
	    	'user'=>null,
	    	'pass'=>null,
	    	'path'=>null,
	    	'query'=>null,
	    	'fragment'=>null
		);

		$lParts=array_replace($lDefaultParts, $lParts);
		
		if (is_null($lParts['port'])) {
			if (!is_null($lParts['scheme'])) {
				switch(strtolower($lParts['scheme'])) {
					case 'https': $lPort=443; 
						break;
					case 'http': $lPort=80; 
					default:
						break;
				}
			} else {
				$lPort=80;
			}	
		} else {
			$lPort=$lParts['port'];
		}
		
		if (is_null($argIp)) {
			$lHost=$lParts['host'];
		} else {
			$lHost=$argIp;
		}

		if (is_null($argConnectionTimeout) || !is_numeric($argConnectionTimeout)) {
			$lFp = @fsockopen($lHost, $lPort, $lErrNo, $lErrStr);
		} else {
			$lFp = @fsockopen($lHost, $lPort, $lErrNo, $lErrStr, $argConnectionTimeout/1000.0);
		}

		if ($lFp===false) {
			throw new Exception('Unable to open a connection to '.$lHost);
		}

		if (!is_null($argCommunicationTimeout) && is_numeric($argCommunicationTimeout)) {
			// set the data write timeout
			
			$lCommunicationTimeout=$argCommunicationTimeout/1000.0;
			
			$lCommunicationTimeoutSeconds=floor($lCommunicationTimeout);
			$lCommunicationTimeoutMicroSeconds=round(($lCommunicationTimeout-$lCommunicationTimeoutSeconds)*1000000);
			
			stream_set_timeout($lFp, $lCommunicationTimeoutSeconds, $lCommunicationTimeoutMicroSeconds);
		}

		$lPostString = http_build_query($argParameters);
		
		$lOut = "POST ".$lParts['path']." HTTP/1.1\r\n";
		$lOut.= "Host: ".$lParts['host']."\r\n";
		$lOut.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$lOut.= "Content-Length: ".strlen($lPostString)."\r\n";
		$lOut.= "Connection: Close\r\n\r\n";
		if (isset($lPostString)) $lOut.= $lPostString;

		fwrite($lFp, $lOut);
		fclose($lFp);	
	} catch (Exception $e) {
		throw $e;
	}
}
