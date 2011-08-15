<?php

/**
 * WikiaApiLyricwiki
 *
 * @author Sean Colombo <sean@wikia.com>
 * @author Lucas Garczewski <tor@wikia-inc.com>
 * 
  Problems:
   - The headers aren't being set (which is important for caching).  Find out where this page is called from.
   - getHometown wasn't ported over.
   - a lot seems to be missing... should make one pass over the code next to the original code & see what's up.
   - Refactor to make it so that less format types need to handle each API call explicitly (kind of like realJSON but for other types).  Probably only HTML (and maybe XML) needs to be handled explicitly.
   - While this is surprisingly easy to modify and doesn't have known bugs, this code is still totally ugly. Would be nice to make a good set of unit-tests (to prevent regressions) and clean up this whole ugly mess.
   - The REST API (this file) technically wraps the SOAP API functions. If possible, it would be nice to extract that to a third file which doesn't have the SOAP code at the top since the REST and SOAP are logically two ways to access the same data.
   - Refactor inconsistent casing so that dumpXML and writeRealJSON are dumpXml and writeRealJson, etc..
   - Refactor to use wgRequest instead of getVal and then delete getVal (profile this change... it probably is negligible difference, but is worth checking.
 */

$wgAPIModules['lyrics'] = 'WikiaApiLyricwiki';

class WikiaApiLyricwiki extends ApiBase {

	var $root = "http://lyrics.wikia.com/"; // for links - TODO: This is an unhelpful var name. Fix it. - SWC

	/**
	 * constructor
	 */
	public function __construct($query, $moduleName) {
		parent :: __construct($query, $moduleName );
	}

	/**
	 * main function
	 */
	public function execute() {
		global $IP;

		define('LYRICWIKI_SOAP_FUNCS_ONLY', true);
		require( "$IP/extensions/3rdparty/LyricWiki/server.php" );

		$func = $song = $artist = $fmt = null;
		$matches = array();

		extract( $this->extractRequestParams() );

		// TODO: Detect the API even if func is not defined (since that wasn't a documented requirement).  - SWC
		$func = (($func == "")?"getSong":$func);

		// Phase 'title' out (deprecated).  this is not the same as the soap.  I was coding too fast whilst in an IRC discussion and someone said artist/title just for the sake of argument and I didn't check against the SOAP :[ *embarassing*
		$song = getVal($_GET, 'song', getVal($_GET, 'title'));
		$artist = getVal($_GET, 'artist');
		$albumName = getVal($_GET, 'albumName');
		$albumYear = getVal($_GET, 'albumYear');

		// Special case (suggested by CantoPod) to return all of an artist's songs when no song is specified.
		// Similarly, if the title passed in is an album, automatically detect that type of page and use the appropriate parser.
		if(($func == "getSong") && ($song == "")){
			$func = "getArtist";
		} else if(($func == "getSong") && (0 < preg_match("/^(.*) \(([0-9]{4})\)$/i", $song, $matches))){
			$albumName = $matches[1];
			$albumYear = $matches[2];
			$func = "getAlbum";
		}

		switch ( $func ) {
			case 'getArtist':
				$this->rest_getArtist( $artist, $fmt );
				break;
			case 'getTopSongs':
				$limit = getVal($_GET, 'limit');
				$this->rest_getTopSongs( $limit, $fmt );
				break;
			case 'getSOTD':
			case 'getSotd':
				$this->rest_getSotd( $fmt );
				break;
			case 'getAlbum':
				// Be gentle and let calling client optionally specify "album" (with name and year) instead of albumName
				// and albumYear separately. If all are provided and 'album' parses correctly, then "album" will override.
				if(("$albumName$albumYear" == "") && (getVal($_GET, 'album') != "")){
					$album = getVal($_GET, 'album');
					if(0 < preg_match("/^(.*) \(([0-9]{4})\)$/i", $album, $matches)){
						$albumName = $matches[1];
						$albumYear = $matches[2];
					}
				}
				$this->rest_getAlbum( $artist, $albumName, $albumYear, $fmt );
				break;
			case 'getSong':
			default:
				$this->rest_getSong( $artist, $song, $fmt );
				break;
		}

		// TODO: hand over handling to MW API instead of doing this...
		exit (1);
	}

	/**
	 * REST endpoint for the getArtist() LyricWiki API function.  Calls the LyricWiki SOAP function which
	 * is currently in /extensions/3rdparty/LyricWiki/server.php.
	 */
	function rest_getArtist( $artist, $fmt ) {
		if(empty( $fmt )){
			$fmt = 'html';
		}

		switch ( $fmt ) {
			case 'text':
				$result = getArtist($artist);

				// This is just a raw line-delimited list of tracks.
				$artist = getVal($result, 'artist');
				$albums = $result['albums'];
				foreach($albums as $currAlbum){
					$albumName = getVal($currAlbum, 'album');
					$year = getVal($currAlbum, 'year');
					$amznLink = getVal($currAlbum, 'amazonLink');
					$songs = getVal($currAlbum, 'songs');
					sort($songs);
					foreach($songs as $currSong){
						if(strpos($currSong, ":") !== false){
							print "$currSong\n"; // a track listing that already has the artist in it
						} else {
							print "$artist:$currSong\n";
						}
					}
				}
				break;
			case "realjson":
				$result = getArtist($artist);
				$this->writeRealJSON($result);
				break;
			case "xml" :
				header('Content-Type: application/xml', true);
				print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
				//print "<getArtistResponse>\n";
				$result = getArtist($artist);
				$result = array("getArtistResponse" => $result);
				$this->dumpXML($result);
				//print "</getArtistResponse>\n";
				break;
			case 'html':
			default:
				$this->htmlHead("$artist");

				$result = getArtist($artist);
				$artist = getVal($result, 'artist');
				$albums = $result['albums'];
				print "<h3><a href='$this->root".$this->linkEncode($artist)."'>$artist</a></h3>\n";
				if(count($albums) > 0){
					print "<ul class='albums'>\n";
					foreach($albums as $currAlbum){
						$albumName = getVal($currAlbum, 'album');
						$year = getVal($currAlbum, 'year');
						$amznLink = getVal($currAlbum, 'amazonLink');
						$songs = getVal($currAlbum, 'songs');
						print "<li><a href='$this->root".$this->linkEncode("$artist:$albumName".($year==""?"":"_($year)"))."'>$albumName".($year==""?"":"_($year)")."</a>";
						if($amznLink != ""){
								print " - (at <a href='$amznLink' title=\"$albumName at amazon\">amazon</a>)";
						}
						if(count($songs) > 0){
							print "<ul class='songs'>\n";
							foreach($songs as $currSong){
								if(strpos($currSong, ":") !== false){
									print "<li><a href='$this->root".$this->linkEncode($currSong)."'>$currSong</a></li>\n";
								} else {
									print "<li><a href='$this->root".$this->linkEncode("$artist:$currSong")."'>$currSong</a></li>\n";
								}
							}
							print "</ul>\n";
						}
						print "</li>\n";
					}
					print "</ul>\n";
				}

				// Make it extensible by displaying any extra data in a UL.
				unset($result['artist']);
				unset($result['albums']);
				if(count($result) > 0){
					print "<hr/>Additional Info:\n";
					print "<ul>\n";
					foreach($result as $keyName=>$val){
						if(0 < preg_match("/^http:\/\//", $val)){
							$val = "<a href='".str_replace(" ", "_", $val)."' title='$keyName'>$val</a>\n";
							print "<li><strong>$keyName: </strong>$val</li>\n";
						} else {
							print "<li><strong>$keyName: </strong>$val</li>\n";
						}
					}
					print "</ul>\n";
				}
				print "</body>\n</html>\n";
				break;
		}
	} // end rest_getArtist()
	
	/**
	 * REST wrapper to the SOAP's getAlbum function.  This returns the discography
	 * for a single album.  Please note that the entry-point can parse 'album' into
	 * albumName and albumYear if 'album' is provided in the normal LyricWiki page
	 * title format (such as "Pink Floyd" for 'artist' and "Dark Side Of The Moon (1973)" for
	 * 'album').
	 */
	function rest_getAlbum( $artist, $albumName, $albumYear, $fmt ){
		wfProfileIn( __METHOD__ );

		if(empty( $fmt )){
			$fmt = 'html';
		}

		$result = getAlbum($artist, $albumName, $albumYear);
		switch ( $fmt ) {
			case 'text':
				$this->dumpText($result);
				break;
			case 'json':
			case 'realjson':
				$this->writeRealJSON($result);
				break;
			case 'xml':
				// TODO: IMPLEMENT
			case 'html':
			default:
				$albumName = getVal($result, 'album');
				$year = getVal($result, 'year');
				$amznLink = getVal($result, 'amazonLink');
				$songs = getVal($result, 'songs');
				print "<a href='$this->root".$this->linkEncode("$artist:$albumName".($year==""?"":"_($year)"))."'>$albumName".($year==""?"":"_($year)")."</a>";
				if($amznLink != ""){
						print " - (at <a href='$amznLink' title=\"$albumName at amazon\">amazon</a>)";
				}
				if(count($songs) > 0){
					print "<ul class='songs'>\n";
					foreach($songs as $currSong){
						if(strpos($currSong, ":") !== false){
							print "<li><a href='$this->root".$this->linkEncode($currSong)."'>$currSong</a></li>\n";
						} else {
							print "<li><a href='$this->root".$this->linkEncode("$artist:$currSong")."'>$currSong</a></li>\n";
						}
					}
					print "</ul>\n";
				}
				print "</li>\n";
				break;
		}
		
		wfProfileOut( __METHOD__ );
	} // end rest_getAlbum()

	// Returns the current most popular songs (right now, powered by iTunes).
	function rest_getTopSongs( $limit=100, $fmt ) {
		if(empty( $fmt )){
			$fmt = 'html';
		}

		$result = getTopSongs($limit);
		switch ( $fmt ) {
		case "xml" :
			header('Content-Type: application/xml', true);
			print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

			// TODO: FIX THIS FORMAT! (each item in the array isn't wrapped. The only well-formed XML is a hack. Figure out a better way to structure the data so that dumpXML can be generic.
				// TODO: Maybe array("topSongs" => array("topSong", $result)) so that it knows what to call each item in the array?
			// TODO: FIX THIS FORMAT! (each item in the array isn't wrapped. The only well-formed XML is a hack. Figure out a better way to structure the data so that dumpXML can be generic.

			$result = array("topSongs" => $result);
			$this->dumpXML($result);
		case "text":
// TODO: IMPLEMENT
// TODO: IMPLEMENT
		case "html":
// TODO: IMPLEMENT
// TODO: IMPLEMENT
		default:
		case "json":
		case "realjson":
			$this->writeRealJSON($result);
			break;
		}
	} // end rest_getTopSongs()
	
	// Returns the Song Of The Day.
	function rest_getSotd(){
		if(empty( $fmt )){
			$fmt = 'html';
		}

		$result = getSOTD();
		switch ( $fmt ) {
		case "xml" :
			header('Content-Type: application/xml', true);
			print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			$result = array("songOfTheDay" => $result);
			$this->dumpXML($result);
		case "text":
// TODO: IMPLEMENT
// TODO: IMPLEMENT
		case "html":
// TODO: IMPLEMENT
// TODO: IMPLEMENT
		default:
		case "json":
		case "realjson":
			$this->writeRealJSON($result);
			break;
		}
	} // end rest_getSotd()

	function rest_getSong( $artist, $songName, $fmt ) {
		wfProfileIn( __METHOD__ );
		global $wgRequest;

		// I'm not sure why, but in this context underscores don't behave like spaces automatically.
		$artist = str_replace("_", " ", $artist);
		$songName = str_replace("_", " ", $songName);

		// Allow debug suffix to persist (needs an underscore instead of a space). It is recommended to use debug=1 instead though.
		$songName = preg_replace("/ debug$/i", "_debug", $songName); // allow the debug suffix to be passed through correctly.

		// Allow "&debug=1" as a URL param option instead of just messing with the song name (cleaner this way).
		$debugMode = $wgRequest->getBool('debug');
		if($debugMode){
			$songName .= "_debug";
		}

		$client = strtolower(getVal($_GET, 'client'));

		if(($client == "cantopod") || ($client == "cantophone")){
			// TODO: SEE IF WE CAN REMOVE cantopod/cantophone hacks

			// Kind of a custom format
			header('Content-Type: application/xml', true);
			print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			print "<item>\n";

			$result = getSong($artist, $songName);
			die( var_dump( $result) );

			if($client == "cantophone"){
				$link = getVal($result, 'url');
				$link = str_replace("http://lyricwiki.org/", "http://www.staylazy.net/canto/online/x/lyricwiki/rss_lyrics.php?artist=", $link);
				$link = preg_replace("/^(.*?):\/\/(.*?):/", "$1://$2&songtitle=", $link);

				print "\t<link>".htmlspecialchars($link, ENT_QUOTES, "UTF-8")."</link>\n";
				print "\t<artist>".htmlspecialchars(getVal($result, 'artist'), ENT_QUOTES, "UTF-8")."</artist>\n";
				print "\t<song>".htmlspecialchars(getVal($result, 'song'), ENT_QUOTES, "UTF-8")."</song>\n";
//				print "\t<lyrics>".htmlspecialchars(getVal($result, 'lyrics'), ENT_QUOTES, "UTF-8")."</lyrics>\n";
			} else {
				foreach($result as $keyName=>$val){
					if($keyName == "url"){
						$keyName = "link";
						$val = str_replace("http://lyricwiki.org/", "http://www.staylazy.net/canto/online/x/lyricwiki/rss_lyrics.php?artist=", $val);
						$val = preg_replace("/^(.*?):\/\/(.*?):/", "$1://$2&songtitle=", $val);
					}
					print "\t<$keyName>".htmlspecialchars($val, ENT_QUOTES, "UTF-8")."</$keyName>\n";
				}
			}
			print "</item>\n";

		} else {
			$lyricsTagFound = false; // will be modified by reference
			$doHyphens = true; $ns = NS_MAIN; $isOuterRequest = true; // optional parameters which we need to hardcode to get to the last parameter
			$result = getSong($artist, $songName, $doHyphens, $ns, $isOuterRequest, $debugMode, $lyricsTagFound);
			
			// Special case: if there was no lyrics tag found, attempt to parse the returned wikitext to look for a tracklisting.
			if( (!$lyricsTagFound) && (0<preg_match("/\[\[.*\]\]/", getVal($result, 'lyrics'))) ){
				// The result wasn't lyrics, but was some other page which appears to contain a tracklisting. Pass off processing to handle that.
				$this->rest_printListing($result['artist'].":".$result['song'], $result['lyrics'], $fmt);
			} else {
				switch($fmt){
				case "text":
					print utf8_decode($result['lyrics']);
					//print "\n\n".$result['url'];
					break;
				case "js":
					$this->writeJS($result);
					break;
				case "json":
					$this->writeJSON_deprecated($result);
					break;
				case "realjson":
					$this->writeRealJSON($result);
					break;
				case "xml":
					header('Content-Type: application/xml', true);
					print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
					print "<LyricsResult>\n";

					// TODO: Would probably be best to extract this whole XMLing function and make it recursive for inner-arrays (look into dumpXML() and why that's not being used here).
					foreach($result as $keyName=>$val){
						if(is_array($val)){
							print "\t<$keyName>\n";
							$innerTagName = $keyName."_value"; // standard name of inner-tag
							
							// If wrapping tag is plural, make inner-tag the singular if that's straightforward.
							$matches = array();
							if(0 < preg_match("/^(.*?)e?s$/i", $keyName, $matches)){
								$innerTagName = $matches[1];
							}

							foreach($val as $innerVal){
								print "\t\t<$innerTagName>".utf8_decode(htmlspecialchars($innerVal, ENT_QUOTES, "UTF-8"))."</$innerTagName>\n";
							}
							print "\t</$keyName>\n";
						} else {
							print "\t<$keyName>".utf8_decode(htmlspecialchars($val, ENT_QUOTES, "UTF-8"))."</$keyName>\n";
						}
					}

					print "</LyricsResult>\n";
					break;
				case "html":
				default:
					// Link to the song & artist pages as a heading.
					$this->htmlHead($result['artist']." ".$result['song']." lyrics");
					print Xml::openElement( 'h3' );
					print Xml::openElement( 'a',
						array(
							'href' => $this->root.$this->linkEncode( $result['artist'].":".$result['song'] )
						)
					);
					print utf8_decode( htmlspecialchars( $result['song'], ENT_QUOTES, "UTF-8" ) );
					print Xml::closeElement( 'a' );
					print ' by ';
					print Xml::openElement( 'a',
						array(
							'href' => $this->root.$this->linkEncode( $result['artist'] )
						)
					);
					print utf8_decode( htmlspecialchars( $result['artist'] , ENT_QUOTES, "UTF-8" ));
					print Xml::closeElement( 'a' );
					print Xml::closeElement( 'h3' );
					print "\n";

					print Xml::openElement( 'pre' );
						print "\n";
						$lyricsHtml = utf8_decode(htmlspecialchars( $result['lyrics'], ENT_QUOTES, "UTF-8" ));

						// Special case to make sure the gracenote copyright symbol gets parsed correctly when needed.
						$lyricsHtml = str_replace("&amp;copy;", "&copy;", $lyricsHtml);

						print $lyricsHtml;
					print Xml::closeElement( 'pre' );

					// Make it extensible by displaying any extra data in a UL.
					unset($result['artist']);
					unset($result['song']);
					unset($result['lyrics']);
					if( count($result) > 0 ){
						print "<hr/> Additional Info:\n";
						print Xml::openElement('ul');
						print "\n";
						foreach($result as $keyName=>$val){
							if(is_array($val)){
								print Xml::openElement( 'li');
								print Xml::openElement( 'strong' );
								print htmlspecialchars( $keyName, ENT_QUOTES, "UTF-8" ).": ";
									print Xml::openElement( 'ul' );
										foreach($val as $innerVal){
											print Xml::openElement( 'li' );

											// TODO: IF keyName == "searchResults", MAKE THESE INTO LINKS TO WIKI PAGES INSTEAD OF JUST PLAINTEXT.

											print $innerVal;
											print Xml::closeElement( 'li' );
										}
									print Xml::closeElement( 'ul' );
								print Xml::closeElement( 'strong' );
								print Xml::closeElement( 'li' );
							} else if(0 < preg_match("/^http:\/\//", $val)){
								print Xml::openElement( 'a',
									array(
										'href' => $val,
										'title' => $keyName
									)
								);
								print utf8_decode( htmlspecialchars( $val, ENT_QUOTES, "UTF-8" ) );
								print Xml::closeElement( 'a' );
								print "\n";
								print Xml::openElement( 'li' );
								print Xml::openElement( 'strong' );
								print htmlspecialchars( $keyName, ENT_QUOTES, "UTF-8" ).": ";
								print Xml::closeElement( 'strong' );
								print htmlspecialchars( $val, ENT_QUOTES, "UTF-8" );
								print Xml::closeElement( 'li');
							} else {
								print Xml::openElement( 'li');
								print Xml::openElement( 'strong' );
								print htmlspecialchars( $keyName, ENT_QUOTES, "UTF-8" ).": ";
								print Xml::closeElement( 'strong' );
								print utf8_decode( htmlspecialchars( $val, ENT_QUOTES, "UTF-8" ) );
								print Xml::closeElement( 'li' );
								print "\n";
							}
						}
						print Xml::closeElement( 'ul' )."\n";
					}
					print Xml::closeElement( 'body' )."\n".Xml::closeElement( 'html' )."\n";
					break;
				} // end switch
			}
		}
		wfProfileOut( __METHOD__ );
	} // end rest_getSong()
	
	/**
	 * This is generally used as a fallback when a page is requested (through one of the other functions) which
	 * doesn't end up being an artist, album, or song as expected but still contains links to other pages.
	 *
	 * The resulting data will just contain the page title and a listing of pages that are linked to from that page.
	 * @param pageTitle - the page on which the listing was found
	 * @param wikiText - the wikitext for the article with the title 'pageTitle'
	 * @param fmt - the output-format (defaults to HTML)
	 */
	public function rest_printListing($pageTitle, $wikiText, $fmt=""){
		wfProfileIn( __METHOD__ );

		// Instead of parseDiscographies() (which is tailored for individual artists) just use regexes to get all links on the page (in order).
		$links = array();
		$matches = array();
		if(0 < preg_match_all("/\[\[(.*?)(\||\]\])/is", $wikiText, $matches)){
			$links = $matches[1];
		}

		// Ouptut the results as just the page title and a listing of pages linked to.
		switch($fmt){
			case "text":
				print utf8_decode(implode($links, "\n"));
				break;
			case "json":
			case "realjson":
				$this->writeRealJSON(array($pageTitle => $links));
				break;
			case "xml":
				header('Content-Type: application/xml', true);
				print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
				print "<listing listingPage=\"".htmlspecialchars($pageTitle)."\">\n";
				foreach($links as $destPageTitle){
					print "\t<pageTitle>$destPageTitle</pageTitle>\n";
				}
				print "</listing>\n";
				break;
			case "html":
			default:
				print Xml::openElement( 'h3' );
				print Xml::openElement( 'a',
					array(
						'href' => $this->root.$this->linkEncode( $pageTitle )
					)
				);
				print utf8_decode( htmlspecialchars( $pageTitle, ENT_QUOTES, "UTF-8" ) );
				print Xml::closeElement( 'a' );
				print Xml::closeElement( 'h3' );
				print Xml::openElement( 'ul' );
					foreach($links as $destPageTitle){
						print Xml::openElement( 'li' );
						print Xml::openElement( 'a',
							array(
								'href' => $this->root.$this->linkEncode( $destPageTitle )
							)
						);
						print utf8_decode( htmlspecialchars( $destPageTitle, ENT_QUOTES, "UTF-8" ) );
						print Xml::closeElement( 'a' );
						print Xml::closeElement( 'li' );
						print "\n";
					}
				print Xml::closeElement( 'ul' );
				break;
		} // end switch

		wfProfileIn( __METHOD__ );
	} // end rest_printListing()

	public function getVersion() {
		return __CLASS__ . ': $Id: WikiaApiQueryDomains.php 12417 2008-05-07 09:33:11Z eloy $';
	}

	public function getDescription() {
		return "Get data for given artist, album, or song.";
	}

	public function getAllowedParams() {
		return array (
			"artist" => array(
				ApiBase::PARAM_TYPE => 'string'
			),
			"song" => array(
				ApiBase::PARAM_TYPE => 'string'
			),
			"albumName" => array(
				ApiBase::PARAM_TYPE => 'string'
			),
			"albumYear" => array(
				ApiBase::PARAM_TYPE => 'integer'
			),
			"fmt" => array(
				ApiBase::PARAM_TYPE => 'string'
			),
			"func" => array(
				ApiBase::PARAM_TYPE => 'string'
			),
			"limit" => array(
				Apibase::PARAM_TYPE => 'integer'
			)
		);
	}

	public function getParamDescription() {
		return array (
			"artist" => "Artist's name",
			"song" => "Song name",
			"albumName" => "Album name",
			"albumYear" => "Four-digit album year",
			"fmt" => "Response format",
			"func" => "Query type",
			"limit" => "Max number of items to return (eg: in getTopSongs)",
		);
	}

	public function getExamples() {
		return array (
			"api.php?action=lyrics&artist=Joe%20Bonamassa&song=So%20Many%20Roads&fmt=xml&func=getSong"
		);
	}

	function htmlHead($title){
        ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php print utf8_decode("$title"); ?></title>
</head>
<body><?php
	}

	////
	// Turns a song-name, link into the format that we'd prefer for linking.
	////
	function linkEncode($pageName){
        	$pageName = str_replace(" ", "_", $pageName);
	        $pageName = urlencode($pageName);
	        $pageName = str_replace("%3A", ":", $pageName);
	        return $pageName;
	}

	////
	// The second parameter is the optional indentation at the start of this item (used for recursion).
	////
	function dumpXML($dataArray, $tabs=""){
        if(is_array($dataArray)){
			$cnt = 0;
			foreach($dataArray as $tag => $val){
				if(is_array($val) && ($cnt === $tag)){
					if(isset($val['album']) && isset($_GET['fixXML'])){ // TODO: HACK: This is actaully lame... what we SHOULD be doing is making a way to name each of these results (so other things can do the same thing that albumResult is doing here).
						print "$tabs<albumResult>\n";
						$tabs = "\t$tabs";
					}
					$this->dumpXML($val, $tabs);
					if(isset($val['album']) && isset($_GET['fixXML'])){
						$tabs = substr($tabs, -1);
						print "$tabs</albumResult>\n";
					}
				} else {
					if($cnt === $tag){
						$tag = "item";
					}
					print "$tabs<$tag>";
					if(is_array($val)){
						print "\n"; // keeps bottom-level items one-liners
					}
					$this->dumpXML($val, "\t$tabs");
					if(is_array($val)){
						print "$tabs";
					}
					print "</$tag>\n";
				}
				$cnt++;
			}
        } else {
			print htmlspecialchars($dataArray, ENT_QUOTES, "UTF-8");
        }
	} // end dumpXML()
	
	/**
	 * Dumps the result in human-readable plaintext.
	 */
	function dumpText($result, $indent=""){
		wfProfileIn( __METHOD__ );
		
		if(count($result) > 0){
			foreach($result as $key => $val){
				if(is_array($val)){
					$this->dumpText($val, " $indent"); // recursively display the rest of the content, indented more.
				} else {
					print "$indent$key: $val\n";
				}
			}
		}

		wfProfileOut( __METHOD__ );
	} // end dumpText()

	//////////////////////////////////////////////////////////////////////////////
	// Thanks to Stefan Fussenegger (Fuzy) for the code which the below code is based on (he released his code to public-domain).
	// NOTE: It turns out this isn't valid HTML.  For future versions of the API, migrate all return-results to use what is now inside of writeRealJSON(). - SWC
	//////////////////////////////////////////////////////////////////////////////
	////
	// escape strings for ' quoted JS strings
	////
	function escapeJavaScript($val, $escapeDoubleQuotesInstead=false) {
		// escape literal backslashes
		$val = str_replace('\\', '\\\\', $val);
		if($escapeDoubleQuotesInstead){
			// escape "
			$val = str_replace('"', '\\"', $val);
		} else {
			// escape '
			$val = str_replace("'", "\\'", $val);
		}
		// replace new lines with \n
		$val = str_replace("\n", "\\n", $val);
		return $val;
	} // escapeJavaScript()

	////
	// create object with JS code
	////
	function writeJS(&$result) {
		header('Content-type: text/javascript; charset=UTF-8');
		echo "function lyricwikiSong(){\n";
		echo "this.artist='".$this->escapeJavaScript(utf8_decode($result['artist']))."';\n";
		echo "this.song='".$this->escapeJavaScript(utf8_decode($result['song']))."';\n";
		echo "this.lyrics='".$this->escapeJavaScript(utf8_decode($result['lyrics']))."';\n";
		echo "this.url='".$this->escapeJavaScript($result['url'])."';\n";
		echo "}\n";
		echo "var song = new lyricwikiSong();\n";
	} // end writeJS()

	////
	// create object using a broken JSON format (oops)
	//
	// This is kept for backwards compatibility since it was written by an app developer who used
	// it in this format.
	////
	function writeJSON_deprecated(&$result) {
		header('Content-type: text/javascript; charset=UTF-8');
		echo "song = {\n";
		echo "'artist':'".$this->escapeJavaScript(utf8_decode($result['artist']))."',\n";
		echo "'song':'".$this->escapeJavaScript(utf8_decode($result['song']))."',\n";
		echo "'lyrics':'".$this->escapeJavaScript(utf8_decode($result['lyrics']))."',\n";
		echo "'url':'".$this->escapeJavaScript($result['url'])."'\n";
		echo "}\n";
	}

	////
	// prints out any result in JSON format.
	////
	function writeRealJSON($result) {
		header('Content-type: text/javascript; charset=UTF-8');

		// TODO: Use this once we are on PHP 5.3 or greater.
		//print json_encode($result, JSON_HEX_APOS);

		// TODO: Do we even need this? It might actually work already.
		//$result = self::replaceApostrophes($result);
		print json_encode($result);
	} // end writeRealJSON()
	
	////
	// Until we upgrade to PHP 5.3 so we can use the options on json_encode, this will
	// help to make the single-quotes safe in the JSON.
	////
	private static function replaceApostrophes($result){
		wfProfileIn( __METHOD__ );

		foreach($result as $key => $val){
			unset($result[$key]); // the key may change after escaping, so remove the old key

			$key = str_replace("'", "\u0027", $key);
			if(is_array($val)){
				$val = self::replaceApostrophes($val);
			} else {
				$val = str_replace("'", "\u0027", $val);
			}
			$result[$key] = $val;
		}

		wfProfileOut( __METHOD__ );
		return $result;
	} // end replaceApostrophes()

}
