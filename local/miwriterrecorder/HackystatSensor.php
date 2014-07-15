<?php
require_once('HTTP/Request2.php');

class HackystatSensor {

    /**
     * constructor
     */
    function __construct() 
	{
        
    }

    public function controller() 
    {
        
    }

    /**
     * destructor
     */
    function __destruct() {
        // do nothing
    }


	/**
	* @author Jeremie S
	* @since 2013-09-26
	* @param undefined $tool
	* @param undefined $sdt
	* @param undefined $resource
	* @param undefined $owner
	* @param undefined $data
	* $data must be in the format 1D array [array(key=>value, key=>value, key=>value, etc.)]
	*/
    public function putSensorData1($tool, $sdt, $resource, $owner, $data)
    {
		
		$t = microtime(TRUE);
		$micro = sprintf("%06d",($t - floor($t)) * 1000000);
		$datetime = new DateTime(date('Y-m-d\TH:i:s.' . $micro,$t));
		$strDate = $datetime->format("Y-m-d\TH:i:s.u");
		$request = new HTTP_Request2("http://hackystat.athabascau.ca:9876/sensorbase/sensordata/clayton.clemens@gmail.com/$strDate/",
			HTTP_Request2::METHOD_PUT);
		$request->setAuth('clayton.clemens@gmail.com','3r3KFpTbnFhB');
		$request->setHeader('Content-type: text/xml; charset=utf-8');
		
		$properties = '<Properties>';
		foreach($data as $key=>$value)
		{
			$properties .= '<Property>';
			$properties .= '<Key>' . $key . '</Key>';
			$properties .= '<Value>' . $value . '</Value>';
			$properties .= '</Property>';
		}
		$properties .= '</Properties>';
		
		$request->setBody('<?xml version="1.0"?>
				<SensorData>
				 <Timestamp>' . $strDate . '</Timestamp>
				 <Runtime>' . $strDate . '</Runtime>
				 <Tool>' . $tool . '</Tool>
				 <SensorDataType>' . $sdt . '</SensorDataType>
				 <Resource>AAT/' . $resource . '</Resource>
				 <Owner>' . $owner . '</Owner>'
				 . $properties . 
			   '</SensorData>');
		try {
		    $response = $request->send();
		    if (200 == $response->getStatus() or 201 == $response->getStatus()) {
		        echo('<p>Success: ' . $response->getStatus() . ' ' . $response->getReasonPhrase() . '</p>');
		    } else {
		        echo('<p>Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase() . '</p>');
		    }
		} catch (HTTP_Request2_Exception $e) {
		    echo ('Error: ' . $e->getMessage());
		}
    }
	
	
	
    public function putSensorData2($host, $username, $password, 
		$tool, $sdt, $resource, $owner, $data)
    {
		$t = microtime(TRUE);
		$micro = sprintf("%06d",($t - floor($t)) * 1000000);
		$datetime = new DateTime(date('Y-m-d\TH:i:s.' . $micro,$t));
		$strDate = $datetime->format("Y-m-d\TH:i:s.u");
		
		$request = new HTTP_Request2("http://$host:9876/sensorbase/sensordata/$owner/$strDate/",
			HTTP_Request2::METHOD_PUT);
		$request->setAuth($username,$password);
		$request->setHeader('Content-type: text/xml; charset=utf-8');
		
		$properties = '<Properties>';
		foreach($data as $key=>$value)
		{
			$properties .= '<Property>';
			$properties .= '<Key>' . $key . '</Key>';
			$properties .= '<Value>' . $value . '</Value>';
			$properties .= '</Property>';
		}
		$properties .= '</Properties>';
		
		$request->setBody('<?xml version="1.0"?>
				<SensorData>
				 <Timestamp>' . $strDate . '</Timestamp>
				 <Runtime>' . $strDate . '</Runtime>
				 <Tool>' . $tool . '</Tool>
				 <SensorDataType>' . $sdt . '</SensorDataType>
				 <Resource>AAT/' . $resource . '</Resource>
				 <Owner>' . $owner . '</Owner>'
				 . $properties . 
			   '</SensorData>');
			
		try {
		    $response = $request->send();
		    if (200 == $response->getStatus()) 
			{
		    }
			else 
			{
				if(201 == $response->getStatus())
				{
					
				}
		       	else
				{
					
					echo('Unexpected HTTP status: ' . $response->getStatus() . ' ' .
		             	$response->getReasonPhrase());
				}
		    }
		} catch (HTTP_Request2_Exception $e) {
		    
			echo ('Error: ' . $e->getMessage());
		}
    }
}
?>
