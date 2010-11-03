<?php

	// **************************************************
	//	create_site_footer
	/*!
		@brief Create footer for site.

		@param $html

		@return None.
	*/
	// **************************************************
	function create_site_footer( $html )
	{
		echo '<div id="footer">';
		echo $html->createLink( 'http://s.runosydan.net', 
			'ShortURL', false );
		echo '. ';

		echo 'Licensed under GNU AGPL, sources available in ';
		echo $html->createLink( 'https://github.com/stargazers/ShortURL-v2',
			'GitHub', true );

		echo  '. (c) ';
		echo $html->createLink( 'mailto:aleksi.rasanen@runosydan.net',
			'Aleksi Räsnen' );

		echo ' 2010. ';
		echo $html->createLink( 'stats.php', 'Stats.', false );
		echo '</div>';
	}

	// **************************************************
	//	create_site_header
	/*!
		@brief Create header for this site.

		@param $html CHTML class instance.

		@return None.
	*/
	// **************************************************
	function create_site_header( $html )
	{
		echo '<div id="top_logo">';
		echo $html->createLink( 'http://s.runosydan.net', 
			'ShortURL', false );
		echo '</div>';
	}

?>
