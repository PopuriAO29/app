<?php

class DiscussionsActivityFormatter extends ContextSource {
	public function __construct( IContextSource $context ) {
		$this->setContext( $context );
	}

	public function formatRow( DiscussionsActivityEvent $discussionsActivityEvent ): string {
		$action = $discussionsActivityEvent->getContentAction();
		$type = $discussionsActivityEvent->getContentType();

		$authorId = $discussionsActivityEvent->getUserId();
		$authorName = User::whoIs( $authorId );

		$userLink = Linker::userLink( $authorId, $authorName ) . ' ' . Linker::userToolLinks( $authorId, $authorName );

		$msg = $this->msg( "discussions-activity-$action-$type" )->rawParams( $userLink )
			->params(
				$discussionsActivityEvent->getUri(),
				$discussionsActivityEvent->getThreadTitle(),
				$discussionsActivityEvent->getForumName() )
			->parse();

		$time = $this->getLanguage()->time( $discussionsActivityEvent->getMwTimestamp(), true, true );

		$row = "$time . . $msg";

		return Html::rawElement( 'li', [], $row );
	}
}
