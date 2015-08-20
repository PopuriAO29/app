<?php

use Wikia\Service\Gateway\ConsulUrlProvider;
use Wikia\Service\Swagger\ApiProvider;
use Swagger\Client\User\Avatars\Api\UserAvatarsApi;
use Swagger\Client\ApiException;

/**
 * A simple wrapper for user avatars service API
 *
 * @author macbre
 * @see PLATFORM-1334
 */
class UserAvatarsService {

	use \Wikia\Logger\Loggable;

	const SERVICE_NAME = 'user-avatar';

	private $mUserId;

	/**
	 * @param int $userId
	 */
	function __construct( $userId ) {
		$this->mUserId = $userId;
	}

	/**
	 * Upload a given file as an avatar for the current user
	 *
	 * Assumes the file to be in a PNG format
	 *
	 * @param string $filePath
	 * @return int UPLOAD_* error code
	 */
	function upload( $filePath ) {
		wfProfileIn( __METHOD__ );

		// prepare the POST parameters
		$postData = [
			'file' => curl_file_create( $filePath, 'image/png', 'avatar.png' )
		];

		try {
			$response = $this->getApiClient()->updateOrCreateUserAvatar( $this->mUserId, $postData );
			wfDebug( __METHOD__ . ': resp - ' . json_encode( $response ) . "\n" );

			$this->info( 'Avatar uploaded', [
				'guid' => $response->imageId
			] );
		}
		catch ( ApiException $e ) {
			wfDebug( __METHOD__ . ': error - ' . $e->getMessage() . "\n" );

			$this->error( 'Avatar upload failed', [
				'exception' => $e,
				'response' => $e->getResponseBody()
			] );

			wfProfileOut( __METHOD__ );
			return UPLOAD_ERR_CANT_WRITE;
		}

		wfProfileOut( __METHOD__ );
		return UPLOAD_ERR_OK;
	}

	/**
	 * Get Swagger-generated API client authenticated for the current user
	 *
	 * @return UserAvatarsApi
	 */
	private function getApiClient() {
		global $wgConsulUrl, $wgConsulServiceTag;

		$urlProvider = new ConsulUrlProvider( $wgConsulUrl, $wgConsulServiceTag );
		$apiProvider = new ApiProvider( $urlProvider );

		return $apiProvider->getAuthenticatedApi( self::SERVICE_NAME, $this->mUserId, UserAvatarsApi::class );
	}

	protected function getLoggerContext() {
		return [
			'class' => __CLASS__,
			'userId' => $this->mUserId
		];
	}
}
