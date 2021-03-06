<?php

/**
 * VideoHandler Helper
 * @author Garth Webb, Hyun Lim, Liz Lee, Saipetch Kongkatong
 */
class VideoHandlerHelper extends WikiaModel {

	/**
	 * Create file page by adding video category
	 *
	 * @param Title|string $title - Title text of a video
	 * @param User|integer $user - A user ID
	 * @param integer $flags - Edit flags to pass to the Article::doEdit method
	 * @return Status|false $status - The status returned by Article::doEdit
	 */
	public function addCategoryVideos( $title, $user, $flags = EDIT_NEW ) {
		wfProfileIn( __METHOD__ );

		if ( is_string( $title ) ) {
			$title = Title::newFromText( $title, NS_FILE );
		}

		$status = false;
		if ( $title instanceof Title && !$title->exists() ) {
			if ( !is_object( $user ) ) {
				$user = User::newFromId( $user );
			}

			$content = '[['.WikiaFileHelper::getVideosCategory().']]';

			$article = new Article( $title );
			$status = $article->doEdit( $content, wfMessage( 'videohandler-log-add-video' )->inContentLanguage()->plain(), $flags, false, $user );
		}

		wfProfileOut( __METHOD__ );

		return $status;
	}

	/**
	 * Get video description, which is the content of the file page minus the category wiki tags
	 *
	 * @param File $file - The file object for this video
	 * @param bool $fillFromMeta - Whether or not to use the video meta description if the current
	 *                             description is blank
	 * @return string $text
	 */
	public function getVideoDescription( $file, $fillFromMeta = true ) {
		wfProfileIn( __METHOD__ );

		// Get the file page for this file
		$page = WikiPage::factory( $file->getTitle() );

		// Strip the description header
		$text = $this->stripDescriptionHeader( $page->getText() );

		// Strip out the category tags so they aren't shown to the user
		$text = FilePageHelper::stripCategoriesFromDescription( $text );

		// If we have an empty string or a bunch of whitespace, and we're asked to do so,
		// use the default description from the file metadata
		if ( $fillFromMeta && ( trim( $text ) == '' ) ) {
			$text = $file->getMetaDescription();
		}

		wfProfileOut( __METHOD__ );

		return $text;
	}

	/**
	 * Add a default video description if one doesn't already exist
	 *
	 * @param $file - The file object for the video
	 * @return bool - Returns true if successful, false otherwise
	 */
	public function addDefaultVideoDescription( File $file ) {
		wfProfileIn( __METHOD__ );

		$title = $file->getTitle();

		// Get the file page for this file
		$page = WikiPage::factory( $title );

		// Get the description and strip the H2 header
		$text = $this->stripDescriptionHeader( $page->getText() );

		// Strip out the category tags that might be part of the content
		$text = FilePageHelper::stripCategoriesFromDescription( $text );

		// If there is no description, pull the description from metadata,
		// otherwise do nothing
		if ( trim( $text ) == '' ) {
			$text = $file->getMetaDescription();
			wfProfileOut( __METHOD__ );
			return $this->setVideoDescription( $title, $text );
		} else {
			wfProfileOut( __METHOD__ );
			return true;
		}
	}

	/**
	 * Replace the description section from $title with the content given by $description.
	 *
	 * @param $title - The DBkey version of a title.
	 * @param $description - The text to use to replace the existing description
	 * @return bool Returns true if successful, false otherwise
	 */
	public function setVideoDescription( $title, $description ) {
		wfProfileIn( __METHOD__ );

		// Get the file page for this file
		$page = WikiPage::factory( $title );

		$text = $page->getText();

		// Insert description header
		$text = $this->replaceDescriptionSection( $text, $description );

		$summary = wfMessage( 'videohandler-log-add-description' )->inContentLanguage()->plain();
		$status = $page->doEdit( $text, $summary );
		$result = $status->isOK();

		wfProfileOut( __METHOD__ );

		return $result;
	}

	/**
	 * Remove description header
	 *
	 * @param string $content
	 * @return string $newContent
	 */
	public function stripDescriptionHeader( $content ) {
		wfProfileIn( __METHOD__ );

		$headerText = wfMessage( 'videohandler-description' );

		// Grab everything after the description header
		preg_match( "/^==\s*$headerText\s*==\n*(.+)/sim", $content, $matches );

		$newContent = '';
		if ( !empty( $matches[1] ) ) {
			// Get rid of any H2 headings after the description
			$newContent = preg_replace( '/^==[^=]+==.*/sm', '', $matches[1] );
		}

		wfProfileOut( __METHOD__ );

		return $newContent;
	}

	/**
	 * Replace the contents of the description section within the content passed in.
	 *
	 * @param string $content - The file page content
	 * @param string $descText - The text to use to replace any existing description section
	 * @return String - The updated file page content
	 */
	public function replaceDescriptionSection( $content, $descText = '' ) {
		wfProfileIn( __METHOD__ );

		$headerText = wfMessage( 'videohandler-description' );

		// Don't include the description section if there's no description text
		$descSection = '';
		if ( trim( $descText ) != '' ) {
			$descSection = "== $headerText ==\n".$descText;
		}

		// Search for the description section in the file page content
		$section = 1;
		$sectionFound = 0;
		$sectionText = '';
		while ( 1 ) {
			// Get section $section to see if its the description
			$sectionText = $this->wg->Parser->getSection( $content, $section );

			// If we find a description header here, exit the loop.  Check for English
			// and the wiki's language
			if ( preg_match( "/^== *(Description|$headerText)/mi", $sectionText ) ) {
				$sectionFound = 1;
				break;
			}

			// If there are no more sections to check, exit the loop
			if ( trim( $sectionText ) == '' ) {
				break;
			}

			$section++;
		}

		// If we found a description section, replace it here
		if ( $sectionFound ) {
			// If there were any categories in the original section, put them back in
			$catText = $this->extractCategories( $sectionText );

			$content = $this->wg->Parser->replaceSection( $content, $section, $descSection."\n".$catText );
		} else {
			// If there wasn't a description section, add one
			$content = $descSection."\n".$content;
		}

		wfProfileOut( __METHOD__ );

		return $content;
	}

	/**
	 * Extract category tags from content text passed in
	 *
	 * @param string $content - Content in which to look for category tags
	 * @return string
	 */
	private function extractCategories( $content ) {
		$catText = '(?:Category|'.wfMessage( 'nstab-category' ).')';
		preg_match_all( "/(\[\[$catText:[^\]]+\]\])/", $content, $matches );

		if ( !empty( $matches[1] ) ) {
			return implode( '', $matches[1] );
		} else {
			return '';
		}
	}

	/**
	 * Add a description header
	 *
	 * @param string $content
	 * @return string $newContent
	 */
	public function addDescriptionHeader( $content ) {
		$newContent = '=='.wfMessage( 'videohandler-description' ).'=='."\n".$content;

		return $newContent;
	}

	/**
	 * get video detail
	 * @param array $videoInfo [ array( 'title' => title, 'addedAt' => addedAt , 'addedBy' => addedBy ) ]
	 * @param array $options
	 *   [ array( 'thumbWidth' => int, 'thumbHeight' => int, 'postedInArticles' => int, 'getThumbnail' => bool, 'thumbOptions' => array ) ]
	 *   Keys:
	 *     thumbWidth - the width of the thumbnail to return (required)
	 *     thumbHeight - the height of the thumbnail to return (required)
	 *     getThumbnail - whether to return a fully formed html thumbnail of the video or not
	 *     thumbOptions - the option of the thumbnail to return
	 *     postedInArticles - the number of "posted in" article details to return
	 * @return array $videoDetail
	 */
	public function getVideoDetail( $videoInfo, $options ) {
		wfProfileIn( __METHOD__ );

		$videoDetail = array();

		/** @var Title $title */
		$title = $videoInfo['title'];
		/** @var LocalFile|WikiaLocalFileShared $file */
		$file = WikiaFileHelper::getVideoFileFromTitle( $title );

		if ( $file ) {
			// get thumbnail
			$thumb = $file->transform( [ 'width' => $options['thumbWidth'], 'height' => $options['thumbHeight'] ] );
			$thumbUrl = $thumb->getUrl();

			// get user
			if ( empty( $videoInfo['addedBy'] ) ) {
				$userName = '';
				$userUrl = '';
			} else {
				$user = User::newFromId( $videoInfo['addedBy'] );
				$userName = ( User::isIP( $user->getName() ) ) ? wfMessage( 'oasis-anon-user' )->text() : $user->getName();
				$userUrl = $user->getUserPage()->getFullURL();
			}

			$thumbnail = '';
			if ( !empty( $options['getThumbnail'] ) ) {
				$thumbOptions = empty( $options['thumbOptions'] ) ? [] : $options['thumbOptions'];

				if ( empty( $thumbOptions['alt'] ) ) {
					$thumbOptions['alt'] = htmlspecialchars( $title->getText() );
				}

				$thumbnail = $thumb->toHtml( $thumbOptions );
			}

			// get article list
			if ( empty( $options['postedInArticles'] ) ) {
				$isTruncated = 0;
				$truncatedList = array();
			} else {
				$mediaQuery = new ArticlesUsingMediaQuery( $title );
				$articleList = $mediaQuery->getArticleList();
				list( $truncatedList, $isTruncated ) = WikiaFileHelper::truncateArticleList( $articleList, $options['postedInArticles'] );
			}

			// video details
			$videoDetail = array(
				'title'                => $title->getDBKey(),
				'fileTitle'            => $title->getText(),
				'description'          => $this->getVideoDescription( $file ), // The description from the File page
				'fileUrl'              => $title->getFullURL(),
				'thumbUrl'             => $thumbUrl,
				'userName'             => $userName,
				'userUrl'              => $userUrl,
				'truncatedList'        => $truncatedList,
				'isTruncated'          => $isTruncated,
				'timestamp'            => empty( $videoInfo['addedAt'] ) ? '' : $videoInfo['addedAt'],
				'duration'             => (float) $file->getMetadataDuration(),
				'viewsTotal'           => empty( $videoInfo['viewsTotal'] ) ? 0 : $videoInfo['viewsTotal'],
				'provider'             => $file->getProviderName(),
				'embedUrl'             => $file->getHandler()->getEmbedUrl(),
				'videoId'              => $file->getVideoId(),
				'thumbnail'            => $thumbnail,
				'regionalRestrictions' => $file->getRegionalRestrictions()
			);
		} else {
			Wikia::Log( __METHOD__, false, "No file found for '".$videoInfo['title']."'" );
		}

		wfProfileOut( __METHOD__ );

		return $videoDetail;
	}

	/**
	 * get select options for template
	 * @param array $options
	 * @param string $selected
	 * @return array $opts
	 */
	public function getTemplateSelectOptions( $options, $selected ) {
		$opts = array();
		foreach ( $options as $key => $value ) {
			$opts[] = array(
				'label' => $value,
				'value' => $key,
				'selected' => ( $key == $selected ),
			);
		}

		return $opts;
	}

	/**
	 * Checks to see if the video title passed in has a thumbnail on disk or not.
	 *
	 * @param string|Title $title - The video title to check
	 * @param boolean $fixit - Whether to fix the problem or ignore it
	 * @return Status
	 */
	public function fsckVideoThumbnail( $title, $fixit = true ) {
		$file = WikiaFileHelper::getVideoFileFromTitle( $title );

		// See if a file exists for this title
		if ( empty( $file ) ) {
			return Status::newFatal( 'File object not found' );
		}

		// See if the thumbnail exists for this title
		if ( file_exists( $file->getLocalRefPath() ) ) {
			return Status::newGood( ['check' => 'ok'] );
		} else {
			// Determine if we should fix this problem or leave it be
			if ( $fixit ) {
				$status = $this->resetVideoThumb( $file );

				if ( $status->isGood() ) {
					return Status::newGood( ['check' => 'failed', 'action' => 'fixed'] );
				} else {
					return $status;
				}
			} else {
				return Status::newGood( ['check' => 'failed', 'action' => 'ignored'] );
			}
		}
	}

	/**
	 * Reset the video thumbnail to its original image as defined by the video provider.
	 * @param File $file The video file to reset
	 * @param string|null $thumbnailUrl
	 * @param int $delayIndex Corresponds to a delay for a job to be queued up if we aren't
	 * able to reset the thumbnail. This index corresponds to a class constant kept in the
	 * ApiWrapper classes.
	 * @return FileRepoStatus The status of the publish operation
	 */
	public function resetVideoThumb( File $file, $thumbnailUrl = null, $delayIndex = 0 ) {
		$mime = $file->getMimeType();
		list(, $provider) = explode('/', $mime);
		$videoId = $file->getVideoId();
		$title = $file->getTitle();

		$oUploader = new VideoFileUploader();
		$oUploader->setProvider( $provider );
		$oUploader->setVideoId( $videoId );
		$oUploader->setTargetTitle( $title->getDBkey() );
		if ( empty( $thumbnailUrl ) ) {
			$thumbnailUrl = $oUploader->getApiWrapper()->getThumbnailUrl();
		}

		$result = $oUploader->resetThumbnail( $file, $thumbnailUrl, $delayIndex );

		if ( $result->isGood() ) {
			// update data and clear cache
			$status = $this->updateThumbnailData( $file );
			if ( !$status->isGood() ) {
				$result->fatal( $status->getMessage() );
			}
		}

		return $result;
	}

	/**
	 * Update thumbnail data (update database and clear cache)
	 * @param File $file
	 * @return Status $status
	 */
	public function updateThumbnailData( $file ) {
		wfProfileIn( __METHOD__ );

		// check for read only mode
		if ( wfReadOnly() ) {
			wfProfileOut( __METHOD__ );
			return Status::newFatal( wfMessage( 'videos-error-readonly' )->plain() );
		}

		$props = $file->repo->getFileProps( $file->getVirtualUrl() );
		if ( empty( $props['size'] ) || empty( $props['width'] ) || empty( $props['height'] )
			|| empty( $props['bits'] ) || empty( $props['sha1'] ) || $props['sha1'] == $file->getSha1() ) {
			wfProfileOut( __METHOD__ );
			return Status::newGood( 0 );
		}

		$dbw = wfGetDB( DB_MASTER );
		$dbw->begin();
		$dbw->update(
			'image',
			array(
				'img_size'   => $props['size'],
				'img_width'  => intval( $props['width'] ),
				'img_height' => intval( $props['height'] ),
				'img_bits'   => $props['bits'],
				'img_sha1'   => $props['sha1'],
			),
			array( 'img_name' => $file->getName() ),
			__METHOD__
		);

		$affected = $dbw->affectedRows();

		$dbw->commit();

		$status = Status::newGood( $affected );
		if ( $affected > 0 ) {
			$file->purgeEverything();
		}

		wfProfileOut( __METHOD__ );

		return $status;
	}
}
