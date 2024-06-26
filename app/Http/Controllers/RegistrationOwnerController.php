<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationOwnerController extends Controller
{
	private $_dbInsert;
	private $_dbSelect;
	private $_newArray;
	
    function RegistrationOwner(Request $array)
	{
		$MatchToken = "";
		
		$input = $array->all();
		if($input["UserPassword"])
		{
			$input["UserPassword"] = password_hash($input["UserPassword"], PASSWORD_DEFAULT);
		}
		
		$this->_dbSelect = DB::select('SELECT username
									FROM users
									WHERE username = ?', 
									[$input["UserName"]]);
		if(!$this->_dbSelect)
		{
			$this->_dbInsert = DB::insert('INSERT into users (username, password, type) 
								values (?, ?, ?)', 
								[$input["UserName"], $input["UserPassword"], "owner"]);
								
			if($this->_dbInsert == 1)
			{
				$temporaryUserId = DB::select('SELECT ID
									FROM users
									WHERE username = ?', 
									[$input["UserName"]]);
									
				foreach($temporaryUserId as $Obj)
				{
							//Here we separete each result from DB in their diferent keys and values
					foreach($Obj as $key => $x)
					{
						$temporaryStore = DB::insert('INSERT into store (IDOwner) 
														values (?)', 
														[$x]);
					}
				}
				
				$MatchToken = true;
				$this->_newArray = array("MatchToken" => $MatchToken);
			}
			else
			{
				$this->_newArray = json_decode('{"ERROR": "Something didn\'t work, Registration was not complet successfuly!"}');
			}
		}
		else
		{
			$MatchToken = false;
			$this->_newArray = array("MatchToken" => $MatchToken);
		}
		return $newArray = $this->_newArray;
	}
}
