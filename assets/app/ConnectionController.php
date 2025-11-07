<?php 

class ConnectionController{

	private $HOST = "127.0.0.1";
	private $USER = "root";
	private $PASS = "root";
	private $DBNM = "enevo";

	function connect()
	{
		$conn = new mysqli($this->HOST,$this->USER,$this->PASS,$this->DBNM);
		if ($conn) {
			return $conn;
		}
		return null;
	}

}

?>