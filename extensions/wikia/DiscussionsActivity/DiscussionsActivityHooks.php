<?php

class DiscussionsActivityHooks {
	public static function onFetchChangesList( User $user, Skin $skin, &$list ): bool {
		$new = $skin->getRequest()->getBool( 'enhanced', $user->getGlobalPreference( 'usenewrc' ) );

		if ( $new ) {
			$list = new DiscussionsAwareEnhancedChangesList( $skin );
		} else {
			$list = new DiscussionsAwareChangesList( $skin );
		}

		return false;
	}
}
