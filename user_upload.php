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
		$msg = createUserTable($username, $password, $host);
			
	}


} else if (array_key_exists('file', $arguments)){ 
	$dryRun = false;
	if (array_key_exists('dry_run', $arguments)){ 
				$dryRun = true;
	}
	// check file exists first
	$file = $arguments['file'];
	$isFile = file_exists($file);
	$fileChunks = explode(".", $file);
	if (end($fileChunks) == 'csv') {
		$isCSV = true;
	} else {
		$isCSV = false;
	}

	
	if (!$isFile || !isCSV) {
		// err msg
		$msg = 'Please specify a valid csv file';
	} else {
		// check file isn't empty
		if (filesize($file) == 0){
			// err msg
			$msg = 'The file specified is empty';
		} else {
		
//			if (array_key_exists('dry_run', $arguments)){ 
//				$dryRun = true;
			if($dryRun) {
				// run through entries
				processFile($file, $dryRun);
				
				// if none valid, err msg, else success msg

			} else {
				// check u/p/h are given
				$uphGiven = checkUPH($arguments);
				if (!$uphGiven[0]) {
					$msg = $uphGiven[1];
				} else {
					$username = $arguments['u'];
					$password = $arguments['p'];
					$host = $arguments['h'];
					//$dryRun = false;
					// run through entries
					processFile($file, $dryRun, $username, $password, $host);
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
	$allGood = true;
	$msg = '';
	
	if (!array_key_exists('u', $arguments)){ 
		$allGood = false;
		$msg .- 'Username is missing. ';
	}
	if (!array_key_exists('p', $arguments)){ 
		$allGood = false;
		$msg .- 'Password is missing. ';	
	}
	if (array_key_exists('h', $arguments)){ 
		$allGood = false;
		$msg .- 'Host is missing. ';
	}
	
	return array($allGood, $msg);
	
}

function createUserTable($username, $password, $host) {
	
	try {
		$pdo = new PDO('pgsql:dbname=usersdb;user=' . $username . ';password=' . $password . ';host=' . $host . ';port=5432');
	
		$sql = "CREATE TABLE IF NOT EXISTS users(
			name VARCHAR (50),
			surname VARCHAR (50),
			email VARCHAR (355) UNIQUE NOT NULL,
			PRIMARY KEY (email)
		);";

		$pdo->execute($sql);
		return 'Users table created';
	} catch(PDOException $e) {
		return 'Unable to create table';
	}


}

function processFile($file, $dryRun, $username='', $password='', $host='') {
	// get file contents
	$userList = file_get_contents($file);
	$userList = explode('\n',$userList);
	
	// line by line check for valid email
	foreach ($userList as $user) {
		$userDets = explode(',', $user);
		if (!filter_var($userDets[0], FILTER_VALIDATE_EMAIL)) {
			// invalid email address
			fwrite(STDOUT, $userDets[0] . ' is not a valid email address');

		} else {
			$email = strtolower($userDets[0]);
			$firstName = ucfirst($userDets[1]);
			$lastName = ucfirst($userDets[2]);

			if (!$dryRun) {
					insertUser($email, $firstName, $lastName, $username, $password, $host);
				
			}
		
		}
	
	}
	

}

function insertUser($email, $firstName, $lastName, $username, $password, $host) {
	try {
		$pdo = new PDO('pgsql:dbname=usersdb;user=' . $username . ';password=' . $password . ';host=' . $host . ';port=5432');
		$sql = 'INSERT INTO users(name,surname,email) VALUES(:nmae,:surname,:email)';
		$stmt->bindValue(':name', $firstName);
		$stmt->bindValue(':surname', $lastName);
		$stmt->bindValue(':email', $email);
        $stmt = $pdo->prepare($sql);
		$stmt->execute();
		
	} catch(PDOException $e) {
			
	}
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