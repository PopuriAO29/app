<?php

class DiscussionsAwareChangesList extends OldChangesList {
	/** @var DiscussionsActivityFormatter $discussionsActivityFormatter */
	private $discussionsActivityFormatter;

	public function __construct( Skin $skin ) {
		parent::__construct( $skin );

		$this->discussionsActivityFormatter = new DiscussionsActivityFormatter( $skin );
	}

	/**
	 * @inheritdoc
	 */
	public function recentChangesLine( &$rc, $watched = false, $linenumber = null ) {
		if ( $rc->getAttribute( 'rc_type' ) == RC_DISCUSSIONS ) {
			$json = json_decode( $rc->getAttribute( 'rc_params' ), true );
			$discussionsActivityEvent = DiscussionsActivityEvent::newFromJson( $json );

			$dateHeader = '';
			$this->insertDateHeader( $dateHeader, $discussionsActivityEvent->getMwTimestamp() );

			return $dateHeader . $this->discussionsActivityFormatter->formatRow( $discussionsActivityEvent );
		}

		return parent::recentChangesLine( $rc, $watched, $linenumber );
	}
}
