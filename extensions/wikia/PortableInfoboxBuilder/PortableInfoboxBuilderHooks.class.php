<?php

class PortableInfoboxBuilderHooks {
	const INFOBOX_BUILDER_SPECIAL_PAGE = 'Special:InfoboxBuilder';

	/**
	 * @param Skin $skin
	 * @param string $text
	 *
	 * @return bool
	 */
	public static function onSkinAfterBottomScripts( $skin, &$text ) {
		$title = $skin->getTitle();

		if ( $title && $title->isSpecial( PortableInfoboxBuilderSpecialController::PAGE_NAME ) ) {
			$scripts = AssetsManager::getInstance()->getURL( 'portable_infobox_builder_js' );

			foreach ( $scripts as $script ) {
				$text .= Html::linkedScript( $script );
			}
		}

		return true;
	}

	/**
	 * Hook that exports url to the current template page
	 *
	 * @param Array $vars - (reference) js variables
	 * @param Array $scripts - (reference) js scripts
	 * @param Skin $skin - skins
	 * @return Boolean True - to continue hooks execution
	 */
	public static function onWikiaSkinTopScripts( &$vars, &$scripts, $skin ) {
		$title = $skin->getTitle();

		if ( $title && $title->isSpecial( PortableInfoboxBuilderSpecialController::PAGE_NAME ) ) {
			// remove the special page name from the title and return url to the template
			// passed after the slash, i.e.
			// Special:InfoboxBuilder/TemplateName/Subpage => Template:TemplateName/Subpage
			$vars['templatePageUrl'] = Title::newFromText(
				implode( '/', array_slice( explode( '/', $title->getText() ), 1 ) ),
				NS_TEMPLATE
			)->getFullUrl();
		}

		return true;
	}

	/**
	 *
	 * @param $page \Article|\Page
	 * @param $user \User
	 * @return bool
	 */
	public static function onCustomEditor( $page, $user ) {
		$title = $page->getTitle();

		if ( self::isEditableInfobox( $title, $user ) ) {
			$url = SpecialPage::getTitleFor( 'InfoboxBuilder', $title->getText() )->getInternalURL();
			F::app()->wg->out->redirect( $url );
			return false;
		}
		return true;

	}

	/**
	 * @param $title
	 * @return bool
	 */
	private static function isInfoboxTemplate( $title ) {
		$tc = new TemplateClassificationService();
		$isInfobox = false;

		try {
			$type = $tc->getType( F::app()->wg->CityId, $title->getArticleID() );
			$isInfobox = ( $type === TemplateClassificationService::TEMPLATE_INFOBOX );
		} catch ( Swagger\Client\ApiException $e ) {
			// If we cannot reach the service assume the default (false) to avoid overwriting data
		}
		return $isInfobox;
	}

	/**
	 * @param $user
	 * @param $title
	 * @return bool
	 */
	private static function isEditableInfobox( $title, $user ) {
		return self::isInfoboxTemplate( $title )
		&& ( new \Wikia\TemplateClassification\Permissions() )->userCanChangeType( $user, $title );
	}
}
