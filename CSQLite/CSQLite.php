<?php

/* 
SQLite database class. 
Copyright (C) 2009 Aleksi Räsänen <aleksi.rasanen@runosydan.net>

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

class CSQLite
{
	//! Database connection
	private $con;

	//! Are we connected or not
	private $connected = false;

	//! Last database query results
	private $lastResults = null;

	//! Last error message
	private $lastError;

	// *******************************************
	//	connect
	//	
	//	@brief Connects to database
	//
	//	@param $db Database
	//
	//	@param $create Create database if not
	//		exists or not.
	//
	// *******************************************
	public function connect( $db, $create = false )
	{
		// File does not exists and we do not want to
		// create new file -> throw Exception.
		if(! file_exists( $db ) && ! $create )
			throw new Exception( 'Database does not exists!' );

		// Try to open database and if it does not exists,
		// try to create it.
		$this->con = @sqlite_open( $db, 0755, $err );

		// If something went wrong, throw Exception.
		if( $err != '' )
		{
			throw new Exception( 'Cannot open or create database. Error: ' . $err );
		}

		$this->connected = true;
		return $this->con;
	}

	// *******************************************
	//	query
	//	
	//	@brief Execute query
	//
	//	@param $q SQL Query
	//
	//	@return Resultset
	//
	// *******************************************
	public function query( $q )
	{
		// Try to create queries only if we have connected
		if(! $this->connected )
			throw new Exception( 'No database connection!' );

		// Execute query 
		$this->lastResults = @sqlite_query( $this->con, $q,
			SQLITE_ASSOC, $err);

		// If query failed, throw an Exception
		if( $err != '' )
		{
			$this->lastError = $err;
			throw new Exception( 'Error in query: ' . $err );
		}

		return $this->lastResults;
	}

	// *******************************************
	//	numRows
	//
	//	@brief Return number of rows in resultset
	//
	//	@param [$ret] If this is given, use this resultset.
	//		Otherwise use last resultset if user has
	//		done at least one query. Otherwise throws
	//		an exception.
	//
	//	@return Number of rows
	//
	// *******************************************
	public function numRows( $ret = '' )
	{
		if( $ret != '' )
			return @sqlite_num_rows( $ret );

		if(! is_null( $this->lastResults ) )
			return @sqlite_num_rows( $this->lastResults );

		throw new Exception( 
			'No resultset given and no queries done!' );
	}

	// *******************************************
	//	getLastInsertID
	//
	//	@brief Get last INSERT ID from database.
	//
	//	@return Last INSERT query ID.
	//
	// *******************************************
	public function getLastInsertID()
	{
		return @sqlite_last_insert_rowid( $this->con );
	}

	// *******************************************
	//	fetchAssoc
	//
	//	@brief Fetch resultset to assoc array
	//
	//	@param [$ret] Resultset. If not given,
	//		use resultset of last query.
	//		If no queries are done, throws
	//		an Exception.
	//
	//	@return Associative array.
	//	
	// *******************************************
	public function fetchAssoc( $ret = '' )
	{
		// Use given resultset if there is any given
		if( $ret != '' )
			return @sqlite_fetch_all( $ret, SQLITE_ASSOC );

		// If no resultset is given, use results of last query
		// if we have done at least one query.
		if(! is_null( $this->lastResults ) )
			return @sqlite_fetch_all( $this->lastResults, 
				SQLITE_ASSOC );

		// No results to fetch -> throw an Exception
		throw new Exception( 
			'No resultset given and no queries done!' );
	}

	
	// *******************************************
	//	disconnect
	//
	//	@brief Close database connection
	//
	// *******************************************
	public function disconnect()
	{
		if( $this->connected )
		{
			sqlite_close( $this->con );
			$this->connected = false;
		}
	}

	// *******************************************
	//	queryAndAssoc
	//
	//	@brief Create SQL query and try to fetch
	//    results to array.
	//
	//	@param $q Query to run. Note! Make sure that
	//	  you have removed illegal characters, this
	//	  function does NOT do it for you!
	//
	//	@return Array of values if there were any
	//	  rows found what to fetch. If there were
	//	  no rows found with query, then return -1.
	//	  If query failed, return -2. Then it is
	//	  better to use normal query instead of
	//	  this for debug, this is useful only when
	//	  you have already made sure that your queries
	//	  will not fail.
	//
	// *******************************************
	public function queryAndAssoc( $q )
	{
		try
		{
			$ret = $this->query( $q );

			if( $this->numRows( $ret ) > 0 )
				return $this->fetchAssoc( $ret );

			return -1;
		}
		catch( Exception $e )
		{
			return -2;
		}
	}
}

?>
