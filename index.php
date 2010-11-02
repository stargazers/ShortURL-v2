<?php

/* 
ShortURL with SQLite 2 database usage.
Copyright (C) 2010 Aleksi Räsänen <aleksi.rasanen@runosydan.net>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require 'CSQLite/CSQLite.php';
require 'CHTML/CHTML.php';

// **************************************************
//	db_connection_failed
/*!
	@brief Show error form when database connection
	  could not be created.

	@param $err Error message what was thrown to function
	  where we called this db_connection_failed function.

	@return None.
*/
// **************************************************
function db_connection_failed( $err )
{
	echo 'Error in database connection! Error was: ' . $err;
}

// **************************************************
//	create_mainpage
/*!
	@brief Shows main page if there is no given ID
	  in GET-parameters.

	@param $db CSQLite database class instance.

	@param $html CHTML class instance.

	@return None.
*/
// **************************************************
function show_mainpage( $db, $html )
{
	$html->createSiteTop( 'ShortURL', 'shorturl.css' );

	echo '<div id="top_logo">';
	echo 'ShortURL';
	echo '</div>';

	// Create form where we can add new items. We just include
	// external HTML form here.
	require 'html/form_add_new.html';

	$html->createSiteBottom();
}

// **************************************************
//	redirect_to
/*!
	@brief Redirect user to given ID if ID exists
	  in database. If it does not exists, we show 
	  error message.

	@param $db CSQLite database class instance.

	@param $html CHTML class instance.

	@param $id ID in database where we redirect user.

	@return None.
*/
// **************************************************
function redirect_to( $db, $html, $id )
{
	// If we get ID what does not exists, we can
	// still redirect user to $url
	$url = 'index.php';

	$id = $html->makeSafeForDB( $id );
	$q = 'SELECT url FROM shorturl WHERE id=' . $id;

	try
	{
		$ret = $db->query( $q );

		if( $db->numRows( $ret ) > 0 )
		{
			$ret = $db->fetchAssoc( $ret );
			$url = $ret[0]['url'];
		}
	}
	catch( Exception $e )
	{
		die( 'Failed! Error was: ' . $e->getMessage() );
	}

	header( 'Location: ' . $url );
}

// **************************************************
//	show_given_shorturl
/*!
	@brief This will show given shortURL to user
	  after INSERT query.

	@param $db CSQLite database class instance.

	@param $id INSERT id for this shortURL.

	@param $html CHTML class instance.

	@return None.
*/
// **************************************************
function show_given_shorturl( $db, $id, $html )
{
	$html->createSiteTop( 'ShortURL', 'shorturl.css' );

	echo '<div id="top_logo">';
	echo 'ShortURL';
	echo '</div>';
	echo '<div id="given_url">';
	echo 'Generated URL is <a href="http://s.runosydan.net/?id='
		. $id . '">http://s.runosydan.net/?id=' . $id . '</a>';
	echo '<br /><br />';
	echo '<a href="index.php">Back to mainpage</a>';
	echo '</div>';

	$html->createSiteBottom();
	die();
}

// **************************************************
//	open_connection
/*!
	@brief Try to open database connection.

	@return CSQLite database class instance if success.
	  If database does not exists with name shorturl.db,
	  then we create it here or at least we try!
	  In case of fail we show error message and die.
*/
// **************************************************
function open_connection()
{
	$db_file = 'shorturl.db';
	$create_db = false;

	$db = new CSQLite();

	// Is database file missing? Create a file, then.
	if(! file_exists( $db_file ) )
	{
		touch( $db_file );
		chmod( $db_file, 0777 );
		$create_db = true;
	}

	try 
	{
		$db->connect( $db_file, false );

		// If we created database file above, we must create
		// also a table for it.
		if( $create_db )
		{
			$q = 'CREATE TABLE shorturl ( id INTEGER PRIMARY KEY, '
				. 'url TEXT, added DATETIME );';
			
			$db->query( $q );
		}
	}
	catch( Exception $e ) 
	{
		// Something failed here. Call db_connection_failed error
		// function and stop executing this file.
		db_connection_failed( $e->getMessage() );
		die();
	}

	return $db;
}

// **************************************************
//	process_post_data
/*!
	@brief Check if there was POST data coming
	  and if so, add data to database.

	@param $db CSQLite database class instance.

	@param $html CHTML class instance.

	@return None.
*/
// **************************************************
function process_post_data( $db, $html )
{
	if(! isset( $_POST['url'] ) )
		return;

	// First remove ', " and others what can explode
	// the whole universe or at least our database :)
	$url = $html->makeSafeForDB( $_POST['url'] );

	// Do not add empty lines.
	if( strlen( trim( $url ) ) == 0 )
		return;

	// Do not add URLs when there is no dot in it.
	if( strstr( $url, '.' ) == false )
		return;

	// Add http:// in the beginning if URL does not
	// start with http or https.
	if( substr( $url, 0, 4 ) != 'http' )
		$url = 'http://' . $url;

	// Add validated URL to database.
	$last_id = add_to_database( $db, $url );

	// Show given shortURL to user too.
	show_given_shorturl( $db, $last_id, $html );
}

// **************************************************
//	add_to_database
/*!
	@brief Adds given URL to the database.

	@param $db CSQLite database class instance.

	@param $url URL. Note! URL must be validated before
	  you call this or the whole universe might explode!

	@return ID of last insert.
*/
// **************************************************
function add_to_database( $db, $url )
{
	// Create database query in variable
	$q = 'INSERT INTO shorturl VALUES( '
		. 'NULL, '
		. '"' . $url . '",'
		. '"' . date( 'Y-m-d H:i:s' ) . '" )';

	try
	{
		$db->query( $q );
	}
	catch( Exception $e )
	{
		die( 'Failed! Error was ' . $e->getMessage() );
	}

	return $db->getLastInsertID();
}

// **************************************************
//	main
/*!
	@brief Main function what will be called first.
	  This exists because I do not like code outside
	  functions.

	@return None.
*/
// **************************************************
function main()
{
	// Try to create database connection. This also checks that
	// database file really exists and creates it if it does not exists.
	$db = open_connection();

	// HTML class for general HTML functions like createSiteTop.
	$html = new CHTML();

	// Check if there is POST data, handle it and try to add them to DB.
	process_post_data( $db, $html );

	// If there was no id given, then we show adding form.
	// If ID was given, then we forward user to correct URL.
	if(! isset( $_GET['id'] ) )
		show_mainpage( $db, $html );
	else
		redirect_to( $db, $html, $_GET['id'] );
}

// Just call our beloved main function.
main();

?>