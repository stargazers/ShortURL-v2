<?php
	
/* 
Stats. Part of ShortURL.
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
	require 'CGeneral/CGeneral.php';
	require 'general_functions.php';

	// **************************************************
	//	main
	/*!
		@brief Main function.
	*/
	// **************************************************
	function main()
	{
		$html = new CHTML();
		$db = new CSQLite();
		$gen = new CGeneral();

		$db->connect( 'shorturl.db' );

		$html->setCSS( 'shorturl.css' );
		echo $html->createSiteTop( 'Statistics' );
		create_site_header( $html );

		$q = 'SELECT * FROM shorturl';
		$ret = $db->queryAndAssoc( $q );

		// Create stastistics array
		$values[] = array( 'ShortURLs in database', count( $ret ) );

		$values[] = array( 'First ShortURL is added', 
			$gen->datetimeToFinnish( $ret[0]['added'] ) );

		$values[] = array( 'Last ShortURL is added', 
			$gen->datetimeToFinnish( $ret[count($ret)-1]['added'] ) );

		// Get info about clicks
		$q = 'SELECT * FROM stats';
		$ret = $db->queryAndAssoc( $q );
		$values[] = array( 'ShortURLs opened', count( $ret ) . ' times' );

		// Get the most clicked URL 
		$q = 'SELECT shorturl, COUNT(shorturl) AS clicks '
			. 'FROM stats GROUP BY shorturl ORDER BY clicks '
			. 'DESC LIMIT 1;';
		$ret = $db->queryAndAssoc( $q );
		$values[] = array( 'Most clicked URL has been clicked',
			$ret[0]['clicks'] . ' times' );

		// Get daily stats.
		$q = 'SELECT strftime( "%d-%m-%Y", visited ) AS day, '
			. 'COUNT(*) AS visits FROM stats GROUP BY '
			. 'strftime( "%d-%m-%Y", visited ) ORDER BY visited DESC;';
		$ret = $db->queryAndAssoc( $q );
		$max = count( $ret );

		// Add daily stats to array $daily_stats
		for( $i=0; $i < $max; $i++ )
		{
			$d = $gen->dateToFinnish( $ret[$i]['day'] );
			$v = $ret[$i]['visits'] . ' links opened';

			$daily_stats[] = array( $d, $v );
		}

		// Show general stats
		echo '<h3>General stats</h3>';
		echo '<div class="stats">';
		echo $html->createTable( $values );
		echo '</div>';

		// Show daily stats
		echo '<h3>Daily stats</h3>';
		echo '<div class="stats">';
		echo $html->createTable( $daily_stats );
		echo $html->createLink( 'index.php', 'Back to main page' );
		echo '</div>';
		create_site_footer( $html );
		echo $html->createSiteBottom();
	}
	
	main();

?>
