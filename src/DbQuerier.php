<?php
namespace tmosest\DbQuerier;

require_once(dirname(__FILE__).'/../config.php');

class DbQuerier
{
    public function __construct(
	    $mySQL,
		$toQuery = true
	)
	{
	    $this->sql = $mySQL;	
		if($toQuery) $this->makeQuery();	
	}
    
    public function setQuery($query, $toQuery = true)
    {
        $this->sql = $query;  
        if($toQuery) $this->makeQuery();	  
    }
		
	private function makeQuery()
	{
	    $this->sqlResults = array();
		$m = mysql_connect(HOST,USER,PASSWORD);
		if($m) {
			mysql_set_charset("utf8");
			$results = mysql_query($this->sql, $m);		
		} else {
			$results = 'Failed to Connect';	
		}
		mysql_close($m);
		while($r = mysql_fetch_assoc($results)) {
	        array_push($this->sqlResults, $r);
		}
	}
		
	public static function pdo_query($sql)
	{
	    $db = new PDO('mysql:host='.HOST.';charset=utf8', USER, PASSWORD);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		try {
			$stmt = $db->query($sql);
			//$row_count = $stmt->rowCount();
   			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $ex) {
			return "An Error occured!"; //user friendly message
			//some_logging_function($ex->getMessage());
		}
	}
		
	public static function pdo_prepareed_query($sql, $args)
	{
	    $db = new PDO('mysql:host='.HOST.';charset=utf8', USER, PASSWORD);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		try {
			$stmt = $db->prepare($sql);
			foreach ($args as $arg) {
			    switch ($arg['type']) {
				    case 'INT':
					    $arg['type'] = PDO::PARAM_INT;
						break;
					case 'STR':
						$arg['type'] = PDO::PARAM_STR;
						break;
                    case 'BOL':
						$arg['type'] = PDO::PARAM_BOOL;
						break;
					default:
						$arg['type'] = PDO::PARAM_STR;
				}
				$stmt->bindValue($arg['id'], $arg['value'], $arg['type']);
			}
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $ex) {
				echo "An Error occured!"; //user friendly message
				//some_logging_function($ex->getMessage());
		}
	}
}//end class
