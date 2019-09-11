<?php

$msg = '';

// get the input arguments

$arguments = getopt('u:p:h:', array('file:', 'create_table', 'dry_run', 'help'));

// select the action based on what's in the arguments


if (array_key_exists('help', $arguments)){ 
	$msg = get_help();

} else if (array_key_exists('create_table', $arguments)){ 
	// check u/p/h are given
	
	
	// check if table exists
	
	
	// create table
	
	
	// success/failure msg


} else if (array_key_exists('file', $arguments)){ 
	// check file exists first
	
	// check file isn't empty
	
	
	
	

	if (array_key_exists('dry_run', $arguments)){ 
		// run through entries
		
		// if none valid, err msg, else success msg

	} else {
		// check u/p/h are given
		
		// run through entries
		
		// if are valid entries insert into db, else no valid, err msg
		
		// msg for insert db success/fail
		
	}


}

// output message

fwrite(STDOUT, $msg);

function get_help() {
	
}


?>