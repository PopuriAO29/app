<?php

/**
 * @ingroup Maintenance
 *
 * @desc For current wiki, find an ID of it's Main Page
 * and push an event marking it as a main page (mainpagefilter_b = 1)
 * to MySqlMetricWorker
 */

require_once( dirname( __FILE__ ) . '../../../Maintenance.php' );

class updateMainPageFilterInMetricProcessor extends Maintenance {
	private $mySQLEventProducer;

	/**
	 * Set script options
	 */
	public function __construct() {
		parent::__construct();
		$this->mDescription = 'updateMainPageFilterInMetricProcessor';
	}

	public function execute() {
		$this->mySQLEventProducer = new \Wikia\IndexingPipeline\MySQLMetricEventProducer();
		$mainPageID = (new Wikia\Search\MediaWikiService)->getMainPageArticleId();
		$this->updateMainPageFilter( $mainPageID );
	}

	public function updateMainPageFilter( $mainPageID ) {
		global $wgCityId;

		$this->mySQLEventProducer->send( $mainPageID, null, null );

		$this->output( sprintf( "%sUpdating data for wiki_id: %d done!%s", PHP_EOL, $wgCityId, PHP_EOL ) );
		return true;
	}
}

$maintClass = 'updateMainPageFilterInMetricProcessor';
require_once( RUN_MAINTENANCE_IF_MAIN );
