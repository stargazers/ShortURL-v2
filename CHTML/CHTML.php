<?php

/* 
CHTML - Class for basic HTML stuff.
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

// **************************************************
//	CHTML
/*!
	@brief Common functions for HTML writing.

	@author Aleksi Räsänen
	  aleksi.rasanen@runosydan.net
*/
// **************************************************
class CHTML
{
	// **************************************************
	//	createRandomString
	/*!
		@brief Generate random alphanumeric string.
		  This can be used in password generations,
		  shorturls and so on.

		@param $len Length of string to generate

		@return Random alpahnumeric string.
	*/
	// **************************************************
    function createRandomString( $len )
    {   
        // Characters what can be in random string
        $ok_val = array();

        // Add number 0-9 
        for( $i=0; $i<10; $i++ )
            $ok_val[] = $i; 

        // Add characters A to Z
        for( $i=65; $i<91; $i++ )
            $ok_val[] = chr( $i );

        // Add characters a to z
        for( $i=97; $i<123; $i++ )
            $ok_val[] = chr( $i );

        $text = ''; 

		// Count size of array $ok_val before
		// for-loop, because we do not want to
		// count it every time because it cannot
		// change on the fly. Optimizations ftw! ;)
        $max = count( $ok_val );

		// Create correct lenght random string
        for( $c=0; $c<$len; $c++ )
            $text .= $ok_val[mt_rand( 0, $max )]; 
    
        return $text;
    }  
	
	// **************************************************
	//	makeSafeForDB
	/*!
		@brief Make text safe for database. Eg. remove
		  ", ', <, and > from text.

		@param $text Original text

		@return Text without ', ", < and > characters.
	*/
	// **************************************************
	public function makeSafeForDB( $text )
	{
		// Values what must be removed before we add 
		// them to database.
		$not_good = array( '"', '\'', '\\', '<', '>' );
		return str_replace( $not_good, '', $text );
	}

	// **************************************************
	//	createSiteTop
	/*!
		@brief Creates HTML headers, eg. <head> etc.
		
		@param $title Page TITLE in <title> element.

		@param $css CSS-filename. Remember path and
		  file prefix too! If this is array, then we add all
		  array items as a CSS file!

		@return None.
	*/
	// **************************************************
	public function createSiteTop( $title = '', $css = '' )
	{
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 '
			. 'Transitional//EN" '
			. '"http://www.w3.org/TR/html4/loose.dtd">';

		echo '<html>';
		echo '<head>';

		echo '<title>' . $title . '</title>';
		echo '<meta http-equiv="Content-Type" '
			. 'content="text/xhtml;charset=utf-8">';

		if(! is_array( $css ) )
		{
			echo '<link rel="stylesheet" type="text/css" '
				. 'href="' . $css . '">';
		}
		else
		{
			foreach( $css as $val )
			{
				echo '<link rel="stylesheet" type="text/css" '
					. 'href="' . $val . '">';
			}
		}
		echo '</head>';

		echo '<body>';
	}

	// **************************************************
	//	createSiteBottom
	/*!
		@brief Create site end, eg. </body> and </html>

		@return None.
	*/
	// **************************************************
	public function createSiteBottom()
	{
		echo '</body>';
		echo '</html>';
	}

	// **************************************************
	//	createLink
	/*!
		@brief Create normal <a href="">URL</a> block.

		@param $url URL inside href part.

		@param $text Text to show in link.

		@param $nw Open in new window? Default false.

		@return $none;
	*/
	// **************************************************
	public function createLink( $url, $text, $nw=false )
	{
		echo '<a href="' . $url . '"';
			
		if( $nw )
			echo ' target="_new"';

		echo '>' . $text . '</a>';
	}

}

?>
