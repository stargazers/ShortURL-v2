<?php
	
	require 'CSQLite/CSQLite.php';
	require 'CHTML/CHTML.php';
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
		$db->connect( 'shorturl.db' );

		echo $html->createSiteTop( 'Statistics', 'shorturl.css' );
		create_site_header( $html );

		$q = 'SELECT * FROM shorturl';
		$ret = $db->queryAndAssoc( $q );

		// Create stastistics array
		$values[] = array( 'ShortURLs in database', count( $ret ) );

		$values[] = array( 'First ShortURL is added', 
			$html->dtToFinnish( $ret[0]['added'] ) );

		$values[] = array( 'Last ShortURL is added', 
			$html->dtToFinnish( $ret[count($ret)-1]['added'] ) );

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

		echo '<div id="stats">';
		echo $html->createTable( $values );
		echo $html->createLink( 'index.php', 'Back to main page' );
		echo '</div>';

		create_site_footer( $html );
		echo $html->createSiteBottom();
	}
	
	main();

?>
