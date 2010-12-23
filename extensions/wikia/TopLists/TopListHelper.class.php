<?php
/**
 * @author Federico "Lox" Lucignano <federico@wikia-inc.com>
 *
 * Utility class for TopLists extension
 */

class TopListHelper {

	/**
	 * @author Federico "Lox" Lucignano
	 *
	 * Main setup function for the TopLists extension
	 */
	public static function setup(){

	}

	/**
	 * @author Federico "Lox" Lucignano
	 *
	 * Callback for the ArticleFromTitle hook
	 */
	public static function onArticleFromTitle( &$title, &$article ) {
		if ( $title->getNamespace() == NS_TOPLIST ) {
			wfLoadExtensionMessages( 'TopLists' );
		}

		return true;
	}

	/**
	 * @author Federico "Lox" Lucignano
	 *
	 * Callback for the AlternateEdit hook
	 */
	public static function onAlternateEdit( &$editPage ) {
		global $wgOut;

		$title = $editPage->getArticle()->getTitle();

		if( $title->getNamespace() == NS_TOPLIST ) {
			//if this is a list item (subpage) then go to the list itself (parent article)
			if( $title->isSubpage() ) {
				$title = Title::newFromText( $title->getBaseText(), NS_TOPLIST );
			}

			$specialPageTitle = Title::newFromText( 'EditTopList', NS_SPECIAL );
			$wgOut->redirect( $specialPageTitle->getFullUrl() . '/' . $title->getDBkey() );
		}

		return true;
	}

	/**
	 * @author Federico "Lox" Lucignano
	 *
	 * Callback for the UserGetRights hook
	 */
	public static function onUserGetRights( $user, &$rights ) {
		global $wgTitle;

		if( $user->isAnon() && $wgTitle instanceof Title && $wgTitle->getNamespace() == NS_TOPLIST ) {
			$key = array_search( 'edit', $rights );

			if ( $key !== false ) {
				unset( $rights[ $key ] );
			}
		}

		return true;
	}

	/**
	 * @author Federico "Lox" Lucignano
	 *
	 * Callback for the getUserPermissionsErrors hook
	 */
	public static function onGetUserPermissionsErrors( &$title, &$user, $action, &$result ) {
		if( $user->isAnon() && $title instanceof Title && $title->getNamespace() == NS_TOPLIST ) {
			if ( $action == 'edit' ) {
				$result = false;
			}

			return false;
		}

		return true;
	}

	/**
	 * @author Federico "Lox" Lucignano
	 *
	 * Callback for the FBConnect::BeforePushEvent hook
	 */
	public static function onBeforePushEvent( $faceBookId, &$message, &$params, &$class ) {
		if(
			!empty( $params[ '$ARTICLE_OBJ' ] ) &&
			$params[ '$ARTICLE_OBJ' ]->getTitle()->getNamespace() == NS_TOPLIST &&
			$params[ '$ARTICLE_OBJ' ]->getTitle()->isSubpage()
		) {
			wfLoadExtensionMessages( 'TopLists' );
			$message = 'toplists-msg-fb-OnRateArticle';
			$parentTitleText = substr( $params[ '$ARTICLENAME' ], 0, strrpos( $params[ '$ARTICLENAME' ], '/' ) );
			$parentTitle = Title::newFromText( $parentTitleText, NS_TOPLIST );

			$params[ '$ARTICLE_OBJ' ] = new Article( $parentTitle );

			$params[ '$ARTICLE_URL' ] = $parentTitle->getFullURL("ref=fbfeed&fbtype=ratearticle");
			$params[ '$ARTICLENAME' ] = $parentTitle->getText();
		}

		return true;
	}
	
	/**
	 * @author Federico "Lox" Lucignano
	 * 
	 * Callback for the ArticleSaveComplete hook
	 */
	public static function onArticleSaveComplete( &$article, &$user, $text, $summary, $flag, $fake1, $fake2, &$flags, $revision, &$status, $baseRevId ) {
		$title = $article->getTitle();
		
		if( ( $title->getNamespace() == NS_TOPLIST ) && !$title->isSubpage() ) {
			self::fixCategorySortKey( $title );
		}
		
		return true;
	}
	
	/**
	 * callback for UnwatchArticleComplete hook
	 *
	 * @author ADi
	 * @param $oUser
	 * @param $oArticle
	 */
	/*
	static public function onUnwatchArticleComplete( &$oUser, &$oArticle ) {
		wfProfileIn( __METHOD__ );

		if ( wfReadOnly() ) {
			wfProfileOut( __METHOD__ );
			return true;
		}

		if ( !$oUser instanceof User ) {
			wfProfileOut( __METHOD__ );
			return true;
		}

		if ( !$oArticle instanceof Article ) {
			wfProfileOut( __METHOD__ );
			return true;
		}

		$oTitle = $oArticle->getTitle();
		if ( !$oTitle instanceof Title ) {
			wfProfileOut( __METHOD__ );
			return true;
		}

		if( $oTitle->getNamespace() == NS_TOPLIST ) {
			$topList = TopListBase::newFromTitle( $oTitle );
			if( $topList instanceof TopListItem ) {
				// item page, do nothing
			}
			elseif ( $topList instanceof TopList ) {
				// list page, unwatch every item page
				foreach( $topList->getItems() as $item ) {
					if( $item->getTitle()->userIsWatching() ) {
						$watchedItem = WatchedItem::fromUserTitle( $oUser, $item->getTitle() );
						$watchedItem->removeWatch();
					}
				}
			}
			//invalidate user cache
			$oUser->invalidateCache();
		}

		wfProfileOut( __METHOD__ );
		return true;
	}
	*/

	/**
	 * callback for WatchArticleComplete hook
	 *
	 * @author ADi
	 * @param $oUser
	 * @param $oArticle
	 */
	/*
	static public function onWatchArticleComplete( &$oUser, &$oArticle ) {
		wfProfileIn( __METHOD__ );

		if( $oTitle->getNamespace() == NS_TOPLIST ) {
			$topList = TopListBase::newFromTitle( $oTitle );
			if( $topList instanceof TopListItem ) {
				// item page, watch the list page as well
				$listTitle = $topList->getList()->getTitle();
				if( !$listTitle->userIsWatching() ) {
					$watchedItem = WatchedItem::fromUserTitle( $oUser, $listTitle );
					$watchedItem->addWatch();
				}
			}
			elseif ( $topList instanceof TopList ) {
				// list page, watch every item page
				foreach( $topList->getItems() as $item ) {
					if( $item->getTitle()->userIsWatching() ) {
						$watchedItem = WatchedItem::fromUserTitle( $oUser, $item->getTitle() );
						$watchedItem->addWatch();
					}
				}
			}
			//invalidate user cache
			$oUser->invalidateCache();
		}

		wfProfileOut( __METHOD__ );
		return true;
	}
	*/

	static public function onComposeCommonSubjectMail( $title, &$keys, &$subject, $editor ) {
		wfProfileIn( __METHOD__ );

		if( ( $title instanceof Title )  && ( $title->getNamespace() == NS_TOPLIST ) ) {
			wfLoadExtensionMessages( 'TopLists' );
			$subject = wfMsg( 'toplists-email-subject' );
		}

		wfProfileOut( __METHOD__ );
		return true;
	}

	static public function onComposeCommonBodyMail( $title, &$keys, &$body, $editor ) {
		wfProfileIn( __METHOD__ );

		if( ( $title instanceof Title )  && ( $title->getNamespace() == NS_TOPLIST ) ) {
			wfLoadExtensionMessages( 'TopLists' );
			$summary = strtr( TOPLISTS_STATUS_SEPARATOR, "\n", $keys['$PAGESUMMARY'] );
			$body = wfMsg( 'toplists-email-body', array( $title->getFullUrl(), $title->getText(), $summary, $title->getFullUrl( 'action=unwatch' ) ) );
		}

		wfProfileOut( __METHOD__ );
		return true;
	}

	/**
	 * @author Federico "Lox" Lucignano
	 *
	 * Callback for the CreatePage::FetchOptions hook
	 */
	static public function onCreatePageFetchOptions( &$options ) {
		global $wgCdnStylePath, $wgScript, $wgShowTopListsInCreatePage;

		if( !empty( $wgShowTopListsInCreatePage ) && Wikia::isOasis() ) {
			wfLoadExtensionMessages( 'TopLists' );

			$specialPageTitle = Title::newFromText( 'CreateTopList', NS_SPECIAL );
			$url = $specialPageTitle->getFullUrl();

			$options[ 'toplist' ] = array(
				'namespace' => NS_TOPLIST,
				'label' => wfMsg( 'toplists-createpage-dialog-label' ),
				'icon' => "{$wgCdnStylePath}/extensions/wikia/TopLists/images/thumbnail_toplist.png",
				'trackingId' => 'toplist',
				'submitUrl' => "{$url}/$1"
			);
		}

		return true;
	}

	/**
	 * @author Federico "Lox" Lucignano
	 *
	 * Callback for the BeforePageDisplay hook
	 */
	public static function onBeforePageDisplay( &$out, &$skin) {
		global $wgTitle, $wgJsMimeType, $wgExtensionsPath, $wgStyleVersion;

		if( $wgTitle->getNamespace() == NS_TOPLIST ) {
			$out->addStyle( wfGetSassUrl( '/extensions/wikia/TopLists/css/list.scss' ) );
			$out->addScript( "<script type=\"{$wgJsMimeType}\" src=\"{$wgExtensionsPath}/wikia/TopLists/js/list.js?{$wgStyleVersion}\"></script>\n" );
		}

		return true;
	}

	public static function onArticleDeleteComplete( &$article, &$user, $reason, $id ) {
		$title = $article->getTitle();

		if( ( $title->getNamespace() == NS_TOPLIST ) && !$title->isSubpage() ) {
			$list = TopList::newFromTitle( $title );
			$list ->removeItems();
		}

		return true;
	}

	public static function onArticleRevisionUndeleted( &$title, $revision, $oldPageId ) {
		if( ( $title->getNamespace() == NS_TOPLIST ) && !$title->isSubpage() ) {
			$list = TopList::newFromTitle( $title );
			$list ->restoreItems();
		}
		return true;
	}

	public static function onTitleMoveComplete( &$title, &$nt, &$user, $pageId, $redirId ) {
		if( ( $title->getNamespace() == NS_TOPLIST ) && !$title->isSubpage() ) {
			$title->moveSubpages( $nt, false );
			self::fixCategorySortKey( $title );
			self::fixCategorySortKey( $nt );
		}
		return true;
	}

	public static function onAddPage( &$categoryPage, &$title, &$titletext, &$sortkey ){
		if( $title->getNamespace() == NS_TOPLIST )
		{
			$titletext = $title->getText();
		}

		return true;
	}

	/**
	 * formats a timespan in a seconds/minutes/hours/days/weeks count string
	 *
	 * @author Federico "Lox" Lucignano
	 *
	 * @param int $seconds
	 * @return string
	 */
	public static function formatTimeSpan( $seconds ) {
		wfLoadExtensionMessages( 'TopLists' );

		if ( $seconds < 60 ) {
			return wfMsgExt( 'toplists-seconds', array( 'parsemag', 'content' ), round( $seconds ) );
		} elseif ( $seconds < 3600 ) {
			return wfMsgExt( 'toplists-minutes', array( 'parsemag', 'content' ), round( $seconds / 60 ) );
		} elseif ( $seconds < ( 3600 * 24 ) ) {
			return wfMsgExt( 'toplists-hours', array( 'parsemag', 'content' ), round( $seconds / 3600 ) );
		} elseif ( $seconds < ( 3600 * 24 * 7 ) ) {
			return wfMsgExt( 'toplists-days', array( 'parsemag', 'content' ), round( $seconds / ( 3600 * 24 ) ) );
		} else {
			return wfMsgExt( 'toplists-weeks', array( 'parsemag', 'content' ), round( $seconds / ( 3600 * 24  * 7 ) ) );
		}
	}

	/**
	 * @author Federico "Lox" Lucignano
	 *
	 * List editor utility function
	 */
	static public function clearSessionItemsErrors() {
		if ( !empty( $_SESSION[ 'toplists_failed_data' ] ) ) {
			unset( $_SESSION[ 'toplists_failed_data' ] );
		}
	}

	/**
	 * @author Federico "Lox" Lucignano
	 *
	 * List editor utility function
	 */
	static public function getSessionItemsErrors() {
		return ( !empty( $_SESSION[ 'toplists_failed_data' ] ) ) ? $_SESSION[ 'toplists_failed_data' ] : array( null, null, null );
	}

	/**
	 * @author Federico "Lox" Lucignano
	 *
	 * List editor utility function
	 */
	static public function setSessionItemsErrors( $listName, $itemNames, $errors ) {
		$_SESSION[ 'toplists_failed_data' ] = array(
			$listName,
			$itemNames,
			$errors
		);
	}

	/**
	 * @author Federico "Lox" Lucignano
	 *
	 * List editor utility function
	 */
	static public function renderImageBrowser() {
		global $wgRequest;
		wfLoadExtensionMessages( 'TopLists' );

		$titleText = $wgRequest->getText( 'title' );
		$selectedImageTitle = $wgRequest->getText( 'selected' );
		$images = array();
		$selectedImage = null;


		if( !empty( $titleText ) ) {
			$title = Title::newFromText( $titleText );

			if( !empty( $title ) && $title->exists() ) {
				$articleId = $title->getArticleId();

				$source = new imageServing(
					array( $articleId ),
					120,
					array(
						"w" => 3,
						"h" => 2
					)
				);

				$result = $source->getImages( 7 );

				if( !empty( $result[ $articleId ] ) ) {
					$images = $result[ $articleId ];
				}
			}
		}

		if( !empty( $selectedImageTitle ) ) {
			$source = new imageServing(
				null,
				120,
				array(
					"w" => 3,
					"h" => 2
				)
			);

			$thumbs = $source->getThumbnails( array( $selectedImageTitle ) );

			if( !empty( $thumbs[ $selectedImageTitle ] ) ) {
				$selectedImage = $thumbs[ $selectedImageTitle ];
			}

		}

		$tpl = new EasyTemplate( dirname( __FILE__ )."/templates/" );

		$tpl->set_vars(
			array(
				'selectedImage' => $selectedImage,
				'images' => $images
			)
		);

		$text = $tpl->execute('image_browser');

		$response = new AjaxResponse( $text );
		$response->setContentType('text/plain; charset=utf-8');

		return $response;
	}

	static public function uploadImage() {
		global $wgRequest, $wgUser, $wgFileExtensions, $wgLang;

		if ( $wgRequest->wasPosted() ) {
			wfLoadExtensionMessages( 'TopLists' );
			
			$ret = array();
			$upload = new UploadFromFile();
			
	                $upload->initializeFromRequest( $wgRequest );
	                $upload->getTitle();//this call is needed, it's not an error, see UploadBase class
			
			$details = $upload->verifyUpload();
			
			if ( $details[ 'status' ] != UploadBase::OK ) {
				$ret[ 'error' ] = true;
				
				switch( $details[ 'status' ] ) {

					/** Statuses that only require name changing **/
					case UploadBase::MIN_LENGTH_PARTNAME:
						$ret[ 'message' ] = wfMsgHtml( 'minlength1' );
						break;
					case UploadBase::ILLEGAL_FILENAME:
						$ret[ 'message' ] = wfMsgExt(
							'illegalfilename',
							'parseinline',
							$details['filtered']
						);
						break;
					case UploadBase::OVERWRITE_EXISTING_FILE:
						$ret[ 'message' ] =  wfMsgExt(
							$details['overwrite'],
							'parseinline'
						);
						break;
					case UploadBase::FILETYPE_MISSING:
						$ret[ 'message' ] = wfMsgExt(
							'filetype-missing',
							'parseinline'
						);
						break;
					case UploadBase::EMPTY_FILE:
						$ret[ 'message' ] = wfMsgHtml( 'emptyfile' );
						break;
					case UploadBase::FILETYPE_BADTYPE:
						$finalExt = $details['finalExt'];
						
						$ret[ 'message' ] = wfMsgExt(
							'filetype-banned-type',
							array( 'parseinline' ),
							htmlspecialchars( $finalExt ),
							implode(
								wfMsgExt(
									'comma-separator',
									array( 'escapenoentities' )
								),
								$wgFileExtensions
							),
							$wgLang->formatNum( count( $wgFileExtensions ) )
						);
						break;
					case UploadBase::VERIFICATION_ERROR:
						unset( $details['status'] );
						$code = array_shift( $details['details'] );
						$ret[ 'message' ] = wfMsgExt(
							$code,
							'parseinline',
							$details['details']
						);
						break;
					case UploadBase::HOOK_ABORTED:
						if ( is_array( $details['error'] ) ) { # allow hooks to return error details in an array
							$args = $details['error'];
							$error = array_shift( $args );
						} else {
							$error = $details['error'];
							$args = null;
						}

						$ret[ 'message' ] = wfMsgExt( $error, 'parseinline', $args );
						break;
					default:
						throw new MWException( __METHOD__ . ": Unknown value `{$details['status']}`" );
				}
			} else {
				$warnings = $upload->checkWarnings();
				
				if ( $warnings ) {
					if (
						!empty( $warnings[ 'exists' ] ) ||
						!empty( $warnings[ 'duplicate' ] ) ||
						!empty( $warnings[ 'duplicate-archive' ] )
					) {
						$ret[ 'conflict' ] = true;
						$ret[ 'message' ] = wfMsg( 'toplists-error-image-already-exists' );
					} else {
						$ret[ 'error' ] = true;
						$ret[ 'message' ] = '';
						
						foreach( $warnings as $warning => $args ) {
							if ( $args === true ) {
								$args = array();
							} elseif ( !is_array( $args ) ) {
								$args = array( $args );
							}

							$ret[ 'message' ] .= wfMsgExt( $warning, 'parseinline', $args ) . "/n";
						}
					}
				} else {
					$status = $upload->performUpload('/* comment */', '/* page text */', false, $wgUser);
					
					if ( !$status->isGood() ) {
						$ret[ 'error' ] = true;
						$ret[ 'message' ] = wfMsg( 'toplists-upload-error-unknown' );
					} else {
						$ret[ 'success' ] = true;
						
						$source = new imageServing(
							null,
							120,
							array(
								"w" => 3,
								"h" => 2
							)
						);

						$thumbs = $source->getThumbnails( array( $upload->getLocalFile() ) );
						$pictureName = $upload->getTitle()->getText();

						if( !empty( $thumbs[ $pictureName ] ) ) {
							$ret[ 'name' ] = $thumbs[ $pictureName ][ 'name' ];
							$ret[ 'url' ] = $thumbs[ $pictureName ][ 'url' ];
						}
					}
				}
			}

			$response = new AjaxResponse('<script type="text/javascript">window.document.responseContent = ' . json_encode( $ret ) . ';</script>');
			$response->setContentType('text/html; charset=utf-8');
			return $response;
		}
	}

	static public function getImageData() {
		global $wgRequest;

		$ret = array( 'result' => false );
		$titleText = $wgRequest->getText( 'title' );

		if( !empty( $titleText ) ) {
			$title = Title::newFromText( $titleText );

			if( !empty( $title ) && $title->exists() ) {
				$articleId = $title->getArticleId();

				$source = new imageServing(
					array( $articleId ),
					120,
					array(
						"w" => 3,
						"h" => 2
					)
				);

				$result = $source->getImages( 1 );

				if( !empty( $result[ $articleId ][ 0 ] ) ) {
					$ret = array_merge( array( 'result' => true ) ,$result[ $articleId ][ 0 ] );
				}
			}
		}

		$json = Wikia::json_encode( $ret );
		$response = new AjaxResponse( $json );
		$response->setContentType( 'application/json; charset=utf-8' );

		return $response;
	}

	/**
	 * Add a vote for the item
	 *
	 * @author ADi
	 *
	 */
	public static function voteItem() {
		global $wgRequest;

		wfLoadExtensionMessages( 'TopLists' );
		$result = array( 'result' => false );

		$titleText = $wgRequest->getVal( 'title' );

		if( !empty( $titleText ) ) {
			$item = TopListItem::newFromText( $titleText );

			if( $item instanceof TopListItem && $item->getList()->exists() && $item->getList()->userCanVote()) {
				$result[ 'result' ] = $item->vote();
				$result[ 'votedId' ] = $item->getTitle()->getSubpageText();
				$result[ 'message' ] = wfMsg( 'toplists-list-item-voted' );
				$result[ 'listBody' ] = TopListParser::parse( $item->getList() );
			}
		}

		$json = Wikia::json_encode( $result );
		$response = new AjaxResponse( $json );
		$response->setContentType( 'application/json; charset=utf-8' );

		return $response;
	}

	/**
	 * check status of the list (vote/edit permissions)
	 */
	public static function checkListStatus() {
		global $wgRequest;

		$result = array(
			'result' => true,
			'canVote' => false,
			/*'ts' => date('Y:m:d H:i:s')*/
			/*'canEdit' => false*/
		);

		$titleText = $wgRequest->getVal( 'title' );

		if( !empty( $titleText ) ) {
			$list = TopList::newFromText( $titleText );

			if( $list instanceof TopList ) {
				$result['canVote'] = $list->userCanVote();
				//$result['canEdit'] = false; // TODO: implement, dropped for the moment
			}
		}

		$json = Wikia::json_encode( $result );
		$response = new AjaxResponse( $json );
		$response->setContentType( 'application/json; charset=utf-8' );

		return $response;
	}

	public static function addItem() {
		global $wgRequest, $wgUser, $wgScript;
		wfLoadExtensionMessages( 'TopLists' );

		$result = array( 'result' => false );
		$errors = array();

		$listText = $wgRequest->getVal( 'list' );
		$itemText = trim( $wgRequest->getVal( 'text' ) );

		if( !empty( $listText ) && !empty( $itemText ) ) {

			$list = TopList::newFromText( $listText );

			if( $wgUser->isAllowed( 'toplists-create-item' ) ) {
				if( !empty( $list ) && $list->exists() ) {
					//check for duplicated
					foreach( $list->getItems() as $item ) {
						if( strtolower( $item->getArticle()->getContent() ) == strtolower( $itemText ) ) {
							$errors[] = wfMsg( 'toplists-error-duplicated-entry' );
							break;
						}
					}

					if( empty( $errors ) ) {
						$newItem = $list->createItem();
						$newItem->setNewContent( $itemText );

						$saveResult = $newItem->save( TOPLISTS_SAVE_CREATE );

						if ( $saveResult !== true ) {
							foreach ( $saveResult as $errorTuple ) {
								$errors[] =  wfMsg( $errorTuple[ 'msg' ], $errorTuple[ 'params' ] );
							}
						} else {
							//invalidate caches and trigger save event for the list article
							$newItem->getTitle()->invalidateCache();
							$list->save( TOPLISTS_SAVE_UPDATE );
							$list->invalidateCache();

							$result['result'] = true;
							$result['listBody'] = TopListParser::parse( $list );
						}
					}
				} else {
					$errors[] = wfMsg( 'toplists-error-add-item-list-not-exists', $listText );
				}
			} else {
				if( $wgUser->isAnon() ) {
					$loginURL = SpecialPage::getTitleFor( 'Signup' )->getLocalURL() . '?returnto=' . $list->getTitle()->getPrefixedDBkey();
					$errors[] = wfMsg(
						'toplists-error-add-item-anon',
						array(
							 "{$loginURL}&type=login",
							"{$loginURL}&type=signup"
						)
					);
				} else {
					$errors[] = wfMsg( 'toplists-error-add-item-permission' );
				}
			}
		} else {
			$errors[] = wfMsg( 'toplists-error-empty-item-name' );
		}

		if( !empty( $errors ) ) {
			$result[ 'errors' ] = $errors;
		}

		$json = Wikia::json_encode( $result );
		$response = new AjaxResponse( $json );
		$response->setContentType( 'application/json; charset=utf-8' );

		return $response;
	}
	
	static public function fixCategorySortKey( Title $title ) {
		
		if( $title->exists() ) {
			$dbw = wfGetDB( DB_MASTER );
			$dbw->update(
				'categorylinks',
				array(
					'cl_sortkey' => $title->getText()
				),
				array( 'cl_from' => $title->getArticleID() ),
				__METHOD__
			);
		}
	}
}

