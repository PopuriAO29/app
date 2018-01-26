<?php

class DiscussionsAwareEnhancedChangesList extends EnhancedChangesList {
	/** @var DiscussionsActivityFormatter $discussionsActivityFormatter */
	private $discussionsActivityFormatter;

	public function __construct( Skin $skin ) {
		parent::__construct( $skin );

		$this->discussionsActivityFormatter = new DiscussionsActivityFormatter( $skin );
	}

	/**
	 * @inheritdoc
	 */
	public function recentChangesLine( &$baseRC, $watched = false ) {
		if ( $baseRC->getAttribute( 'rc_type' ) == RC_DISCUSSIONS ) {
			$json = json_decode( $baseRC->getAttribute( 'rc_params' ), true );
			$discussionsActivityEvent = DiscussionsActivityEvent::newFromJson( $json );

			$dateHeader = '';
			$this->insertDateHeader( $dateHeader, $discussionsActivityEvent->getMwTimestamp() );

			return $dateHeader . $this->discussionsActivityFormatter->formatRow( $discussionsActivityEvent );
		}

		return parent::recentChangesLine( $baseRC, $watched );
	}
}
