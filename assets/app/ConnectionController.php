<?php 

class ConnectionController{

	private $HOST = "127.0.0.1";
	private $USER = "root";
	private $PASS = "";
	private $DBNM = "enevo";
	private $PORT = 3308;  

	function connect()
	{
		$conn = new mysqli($this->HOST, $this->USER, $this->PASS, $this->DBNM, $this->PORT);  // ← Agregar $this->PORT
		if ($conn) {
			return $conn;
		}
		return null;
	}

}

?>