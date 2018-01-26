<?php

class DiscussionsActivityController extends WikiaController {
	public function index() {
		$this->response->setFormat( WikiaResponse::FORMAT_JSON );
		$this->response->setCacheValidity( WikiaResponse::CACHE_DISABLED );

		if ( !$this->request->isInternal() ) {
			$this->response->setCode( WikiaResponse::RESPONSE_CODE_FORBIDDEN );
			return;
		}

		$wikiId = $this->request->getVal( 'wikiId' );
		$dbName = WikiFactory::IDtoDB( $wikiId );

		if ( $dbName ) {
			$body = file_get_contents( 'php://input' );
			$json = json_decode( $body, true );

			$discussionsActivityEvent = DiscussionsActivityEvent::newFromJson( $json );

			$dbr = wfGetDB( DB_MASTER, [], $dbName );

			$row = [
				'rc_type' => RC_DISCUSSIONS,
				'rc_params' => json_encode( $discussionsActivityEvent ),
				'rc_timestamp' => $discussionsActivityEvent->getMwTimestamp(),
			];

			$dbr->insert( 'recentchanges', [ $row ], __METHOD__ );
		}
	}
}
