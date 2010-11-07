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
	//! Messages what will be shown with getMessage will be stored here.
	private $message = array();

	//! Javascript files to include in <head> 
	private $javascript_files;

	// **************************************************
	//	__construct
	/*!
		@brief Create $message array indexes.

		@return None.
	*/
	// **************************************************
	public function __construct()
	{
		// Initialize values, no undefined indexes wanted here.
		$this->message['message'] = '';
		$this->message['code'] = '';
		$this->message['type'] = '';

		// Set empty string to javascript_files
		$this->javascript_files = '';
	}

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
    public function createRandomString( $len )
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
	//	javascriptFiles
	/*!
		@brief Include JavaScript files in <head> part.
		  NOTE! This MUST be called BEFORE you call 
		  method createSiteTop!

		@param $src Source file. If array, multiple files
		  can be added.
	*/
	// **************************************************
	public function javascriptFiles( $src )
	{
		$this->javascript_files = $src;
	}

	// **************************************************
	//	includeCSS
	/*!
		@brief Private method what will include wanted CSS
		  files in to the <head> part. This will be called
		  by createSiteTop.

		@param $css CSS-files to include.

		@return String.
	*/
	// **************************************************
	private function includeCSS( $css )
	{
		$out = '';

		// Only one CSS?
		if(! is_array( $css ) )
		{
			$out .= '<link rel="stylesheet" type="text/css" '
				. 'href="' . $css . '">' . "\n";
		}
		// Multiple CSS files.
		else
		{
			foreach( $css as $val )
			{
				$out .= '<link rel="stylesheet" type="text/css" '
					. 'href="' . $val . '">' . "\n";
			}
		}

		return $out;
	}

	// **************************************************
	//	includeJavascript
	/*!
		@brief Private method what will include javascript
		  files to code. This is called from createSiteTop
		  and this read Javascript files from $this->javascript_files

		@return String.
	*/
	// **************************************************
	private function includeJavascript()
	{
		$out = '';

		// Check if there is any Javascript files to include
		$js = $this->javascript_files;

		// If we have only one Javascript file to include.
		if( $js != '' && ! is_array( $js ) )
		{
			$out .= '<script type="text/javascript" src="' 
				. $js . '"></script>' . "\n";
		}
		// If we have many javascript files to include.
		else if( is_array( $js ) )
		{
			foreach( $js as $val )
			{
				$out .= '<script type="text/javascript" src="'
					. $js . '"></script>' . "\n";
			}
		}

		return $out;
	}

	// **************************************************
	//	createSiteTop
	/*!
		@brief Creates HTML headers, eg. <head> etc.
		  If includeJavascriptFiles method is called before
		  this method, we also include file/files given
		  with that mehtod.
		
		@param $title Page TITLE in <title> element.

		@param $css CSS-filename. Remember path and
		  file prefix too! If this is array, then we add all
		  array items as a CSS file!

		@return Generated HTML in string.
	*/
	// **************************************************
	public function createSiteTop( $title = '', $css = '' )
	{
		$out = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 '
			. 'Transitional//EN" '
			. '"http://www.w3.org/TR/html4/loose.dtd">' . "\n";

		$out .= '<html>' .  "\n";
		$out .= '<head>' . "\n";

		$out .= '<title>' . $title . '</title>' . "\n";
		$out .= '<meta http-equiv="Content-Type" '
			. 'content="text/xhtml;charset=utf-8">' . "\n";

		$out .= $this->includeCSS( $css );
		$out .= $this->includeJavascript();

		$out .= '</head>' . "\n\n";
		$out .= '<body>' . "\n";

		return $out;
	}

	// **************************************************
	//	createSiteBottom
	/*!
		@brief Create site end, eg. </body> and </html>

		@return String where we have generated HTNL.
	*/
	// **************************************************
	public function createSiteBottom()
	{
		$out = '</body>';
		$out .= '</html>';
		return $out;
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
		$out = '<a href="' . $url . '"';
			
		if( $nw )
			$out .= ' target="_new"';

		$out .= '>' . $text . '</a>';
		return $out;
	}

	// **************************************************
	//	dtToFinnish
	/*!
		@brief Convert YYYY-MM-DD H:i:s to finnish
		  way, eg. Date.Month.Year H:i:s

		@param $dt Datetime

		@return Finnish datetime format.
	*/
	// **************************************************
	public function dtToFinnish( $dt )
	{
		return date( 'd.m.Y H:i:s', strtotime( $dt ) );
	}


	// **************************************************
	//	createTable
	/*!
		@brief Create HTML table and add rows CSS class
		  'odd' and 'even'.

		@param $values Array of values.

		@return Generated HTML in string.
	*/
	// **************************************************
	public function createTable( $values )
	{
		$out = '<table>';
		$tmp = 0;

		foreach( $values as $val )
		{
			if( $tmp == 2 )
				$tmp = 0;

			if( $tmp == 0 )
				$out .= '<tr class="odd">';
			else
				$out .= '<tr class="even">';

			$num_vals = count( $val );
			for( $i=0; $i < $num_vals; $i++ )
			{
				$out .= '<td>';
				$out .= $val[$i];
				$out .= '</td>';
			}

			$out .= '</tr>';
			$tmp++;

		}

		$out .= '</table>';
		return $out;
	}

	// **************************************************
	//	setMessage
	/*!
		@brief Set a message in private variable.
		  Message can be shown later with getMessage()

		@param $msg Message

		@param $code Optional. Can be used by user to define
		  own error codes and pass them with messages.

		@param $type Optional. If this is set, then we
		  check in getMessage() method if there is file
		  under ../icons with name $type.png and if so,
		  then we show that icon in return string.

		@return None.
	*/
	// **************************************************
	public function setMessage( $msg, $code='', $type='' )
	{
		$this->message = array( 
				'message' => $msg, 
				'code' => $code,
				'type' => $type );
	}

	// **************************************************
	//	getMessage
	/*!
		@brief Returns private variable $message to caller.

		@return $this->message array.
	*/
	// **************************************************
	public function getMessage()
	{
		return $this->message;
	}

	// **************************************************
	//	showMessage
	/*!
	 	@brief Create a HTML div with message what might
		  be found on $this->message and then returns it.

		@return String
	 */
	// **************************************************
	public function showMessage()
	{
		$m = $this->message;

		if( $m['message'] == '' )
			return '';

		$out = '<div class="message">';

		// If we have defined type for mesage, check if icon file exists.
		if( $m['type'] != '' )
		{
			if( file_exists( 'icons/' . $m['type'] . '.png' ) )
				$out .= '<img src="icons/' . $m['type'] . '.png" />';
		}

		$out .= $m['message'];

		// Create a button where we can close this message.
		$out .= '<a title="Close this message" class="close_icon" '
			. 'href="" onClick="this.display:none">';

		if( file_exists( 'icons/close_message.png' ) )
			$out .= '<img src="icons/close_message.png" /></a>';
		else
			$out .= '(Close)</a>';

		$out .= '</div>';

		// Empty fields after creating this div.
		$fields = array( 'message', 'code', 'type' );
		foreach( $fields as $field )
			$this->message[$field] = '';

		return $out;
	}

	// **************************************************
	//	checkRequiredFields
	/*!
		@brief Checks if all required fields are set

		@param $arr Array where we search

		@param $fields Required fields. This must be array too!

		@return True = All fields are set, false = All fields are not set.
	*/
	// **************************************************
	public function checkRequiredFields( $arr, $fields )
	{
		foreach( $fields as $field )
		{
			if(! isset( $arr[$field] ) )
				return false;
		}

		return true;
	}
}

?>
