<?php

$msg = '';

// get the input arguments

$arguments = getopt('u:p:h:', array('file:', 'create_table', 'dry_run', 'help'));

// select the action based on what's in the arguments

if (array_key_exists('help', $arguments)){ 
	$msg = get_help();

} else if (array_key_exists('create_table', $arguments)){ 
	// check u/p/h are given
	$uphGiven = checkUPH($arguments);
	if (!$uphGiven[0]) {
		$msg = $uphGiven[1];
		
	} else {
		// create table if it doesn't exist
		$username = $arguments['u'];
		$password = $arguments['p'];
		$host = $arguments['h'];
		createUserTable($username, $password, $host);
	
		// success/failure msg
		
	}


} else if (array_key_exists('file', $arguments)){ 
	// check file exists first
	$file = $arguments['file'];
	$isFile = file_exists($file);
	
	if (!$isFile) {
		// err msg
		
	} else {
		// check file isn't empty
		if (filesize($file) == 0){
			// err msg
		} else {
		
			if (array_key_exists('dry_run', $arguments)){ 
				$dryRun = true;
				// run through entries
				processFile($file, $dryRun);
				
				// if none valid, err msg, else success msg

			} else {
				// check u/p/h are given
				$uphGiven = checkUPH($arguments);
				if (!$uphGiven[0]) {
					$msg = $uphGiven[1];
				} else {
					$dryRun = false;
					// run through entries
					processFile($file, $dryRun);
					// if are valid entries insert into db, else no valid, err msg
		
					// msg for insert db success/fail
		
				}
			}
		}
		
	}


}

// output message

fwrite(STDOUT, $msg);

function checkUPH($arguments) {
	
}

function createUserTable($username, $password, $host) {
	
}

function processFile($file, $dryRun) {
	// run through entries
	
}


function get_help() {
	$helpText = "--file [csv file name] – this is the name of the CSV to be parsed
--create_table – this will cause the PostgreSQL users table to be built (and no further action will be taken)
--dry_run – this will be used with the --file directive in case we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered
-u – PostgreSQL username
-p – PostgreSQL password
-h – PostgreSQL host
--help – which will output the above list of directives with details.";

	fwrite(STDOUT, $helpText);
	
}


?>