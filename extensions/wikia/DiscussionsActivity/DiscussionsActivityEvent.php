<?php

class DiscussionsActivityEvent implements JsonSerializable {
	use JsonDeserializerTrait;

	/** @var string $wikiId */
	private $wikiId;

	/** @var string $userId */
	private $userId;

	/** @var string $contentAction */
	private $contentAction;

	/** @var string $contentType */
	private $contentType;

	/** @var string $threadTitle */
	private $threadTitle;

	/** @var string $uri */
	private $uri;

	/** @var string $forumName */
	private $forumName;

	/** @var string $timestamp */
	private $timestamp;

	/** @var string $mwTimestamp */
	private $mwTimestamp;

	/**
	 * @return string
	 */
	public function getWikiId(): string {
		return $this->wikiId;
	}

	/**
	 * @return string
	 */
	public function getUserId(): string {
		return $this->userId;
	}

	/**
	 * @return string
	 */
	public function getContentAction(): string {
		return $this->contentAction;
	}

	/**
	 * @return string
	 */
	public function getContentType(): string {
		return $this->contentType;
	}

	/**
	 * @return string
	 */
	public function getThreadTitle(): string {
		return $this->threadTitle;
	}

	/**
	 * @return string
	 */
	public function getUri(): string {
		return $this->uri;
	}

	/**
	 * @return string
	 */
	public function getForumName(): string {
		return $this->forumName;
	}

	/**
	 * @return string
	 */
	public function getTimestamp(): string {
		return $this->timestamp;
	}

	/**
	 * @return string
	 * @throws MWException
	 */
	public function getMwTimestamp(): string {
		if ( $this->mwTimestamp === null ) {
			$eventTime = new DateTime( $this->timestamp );
			$this->mwTimestamp = wfTimestamp( TS_MW, $eventTime->getTimestamp() );
		}

		return $this->mwTimestamp;
	}

	public function jsonSerialize() {
		return [
			'wikiId' => $this->wikiId,
			'userId' => $this->userId,
			'contentAction' => $this->contentAction,
			'contentType' => $this->contentType,
			'threadTitle' => $this->threadTitle,
			'uri' => $this->uri,
			'forumName' => $this->forumName,
			'timestamp' => $this->timestamp,
		];
	}
}
