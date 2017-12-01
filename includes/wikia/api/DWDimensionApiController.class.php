<?php

class DWDimensionApiController extends WikiaApiController {
	const LIMIT = 100;
	const LIMIT_MAX = 20000;

	const WIKI_DOMAINS_AFTER_DOMAIN = null;

	const WIKIS_AFTER_WIKI_ID = -1;

	const ARTICLES_AFTER_ARTICLE_ID = -1;

	const ARTICLE_LAST_EDITED = '1970-01-01';

	const DART_TAG_VARIABLE_NAME = 'wgDartCustomKeyValues';

	private $connections = [];

	private function getDbSlave( $dbname ) {
		return wfGetDB( DB_SLAVE, array(), $dbname );
	}

	private function getSharedDbSlave() {
		global $wgExternalSharedDB;
		return $this->getDbSlave( $wgExternalSharedDB );
	}

	public function getWikiDartTags() {
		$db = $this->getSharedDbSlave();

		$limit = min($db->strencode( $this->getRequest()->getInt( 'wiki_limit', static::LIMIT ) ), static::LIMIT_MAX);
		$afterWikiId = $db->strencode( $this->getRequest()->getInt( 'after_wiki_id', static::WIKIS_AFTER_WIKI_ID ) );

		$variables = WikiFactory::getVariableForAllWikis( static::DART_TAG_VARIABLE_NAME, $limit, $afterWikiId );

		$result = [];
		foreach ($variables as $variable) {
			#extract from list like "s:199:\"sex=m;sex=f;age=under18;age=13-17;age=18-24;age=25-34;age=18-34;\";"
			preg_match_all("/([^;= ]+)=([^;= ]+)/", $variable[ 'value' ], $r);

			for ($i = 0; $i < count( $r[1] ); $i++) {
				$result[] = [
					'wiki_id' => $variable[ 'city_id' ],
					'tag' => $r[ 1 ][ $i ],
					'value' => $r[ 2 ][ $i ]
				];
			}
		}

		$this->setResponseData(
			$result,
			null,
			WikiaResponse::CACHE_DISABLED
		);
	}

	private function getVerticalName( $allVerticals, $verticalId ) {
		if ( isset( $allVerticals[ $verticalId ] ) ) {
			return $allVerticals[ $verticalId ][ 'name' ];
		}
		return null;
	}

	private function getCategoryName( $allCategories, $categoryId ) {
		if ( isset( $allCategories[ $categoryId ] ) ) {
			return $allCategories[ $categoryId ][ 'name' ];
		}
		return null;
	}

	public function getWikis() {
		$db = $this->getSharedDbSlave();

		$limit = min( $db->strencode( $this->getRequest()->getVal( 'limit', static::LIMIT ) ), static::LIMIT_MAX );
		$afterWikiId = $db->strencode( $this->getRequest()->getVal( 'after_wiki_id', static::WIKIS_AFTER_WIKI_ID ) );

		$query = str_replace( '$city_id', $afterWikiId, DWDimensionApiControllerSQL::DIMENSION_WIKIS_QUERY );
		$query = str_replace( '$limit', $limit, $query);

		$allVerticals = WikiFactoryHub::getInstance()->getAllVerticals();
		$allCategories = WikiFactoryHub::getInstance()->getAllCategories();

		$dbResult = $db->query( $query,__METHOD__);
		$result = [];
		while ( $row = $db->fetchObject( $dbResult ) ) {
			$result[] = [
				'wiki_id' => $row->wiki_id,
				'dbname' => $row->dbname,
				'sitename' => $row->sitename,
				'url' => parse_url($row->url, PHP_URL_HOST),
				'domain' => parse_url($row->url, PHP_URL_HOST),
				'title' => $row->title,
				'founding_user_id' => $row->founding_user_id,
				'public' => $row->public,
				'lang' => $row->lang,
				'lang_id' => $row->lang_id,
				'ad_tag' => $row->ad_tag,
				'category_id' => $row->category_id,
				'category_name' => $this->getCategoryName( $allCategories, $row->category_id ),
				'hub_id' => $row->category_id,
				'hub_name' => $this->getCategoryName( $allCategories, $row->category_id ),
				'vertical_id' => $row->vertical_id,
				'vertical_name' => $this->getVerticalName( $allVerticals, $row->vertical_id ),
				'cluster' => $row->cluster,
				'created_at' => $row->created_at,
				'deleted' => $row->deleted
			];
		}
		$db->freeResult( $dbResult );

		$this->setResponseData(
			$result,
			null,
			WikiaResponse::CACHE_DISABLED
		);
	}

    private function getDataWareDbSlave() {
        global $wgExternalDatawareDB;
        return wfGetDB( DB_SLAVE, array(), $wgExternalDatawareDB );
    }

    public function getAllArticles() {

        $db = $this->getDataWareDbSlave();

        $limit = min($db->strencode( $this->getRequest()->getVal( 'limit', static::LIMIT ) ),
            static::LIMIT_MAX);
        $afterWikiId = $db->strencode( $this->getRequest()->getVal( 'after_wiki_id',
            static::WIKIS_AFTER_WIKI_ID ) );
        $afterArticleId = $db->strencode( $this->getRequest()->getVal( 'after_article_id',
            static::ARTICLES_AFTER_ARTICLE_ID ) );
        $article_last_edited = $db->strencode( $this->getRequest()->getVal( 'article_last_edited',
            static::ARTICLE_LAST_EDITED ) );

        $query = str_replace( '$wiki_id', $afterWikiId,
            DWDimensionApiControllerSQL::DIMENSION_WIKI_ARTICLES_QUERY);
        $query = str_replace( '$article_id', $afterArticleId, $query);
        $query = str_replace( '$last_edited', $article_last_edited, $query);
        $query = str_replace( '$limit', $limit, $query);

        $dbResult = $db->query($query,__METHOD__);
        $result = [];
        while ($row = $db->fetchObject($dbResult)) {
            $result[] = [
                'wiki_id' => $row->wiki_id,
                'namespace_id' => $row->namespace_id,
                'article_id' => $row->article_id,
                'title' => $row->title,
                'is_redirect' => $row->is_redirect,
            ];
        }
        $db->freeResult( $dbResult );

        $this->setResponseData(
            $result,
            null,
            WikiaResponse::CACHE_DISABLED
        );
    }

	public function getWikiEmbeds() {
		$this->getDataPerWiki( array( $this, 'getWikiEmbedsData' ) );
	}

	private function getWikiEmbedsData( $db ) {
		$rows = $db->query( DWDimensionApiControllerSQL::DIMENSION_WIKI_EMBEDS, __METHOD__ );
		$result = [];
		while ( $row = $db->fetchObject( $rows ) ) {
			$result[] = [
				'article_id' => $row->article_id,
				'video_title' => $row->video_title,
				'added_at' => $row->added_at,
				'added_by' => $row->added_by,
				'duration' => $row->duration,
				'premium' => $row->premium,
				'hdfile' => $row->hdfile,
				'removed' => $row->removed,
				'views_30day' => $row->views_30day,
				'views_total' => $row->views_total
			];
		}
		$db->freeResult( $rows );

		return $result;
	}

	public function getWikiImages() {
		$this->getDataPerWiki( array( $this, 'getWikiImagesData' ) );
	}

	private function getWikiImagesData( $db ) {
		$rows = $db->query( DWDimensionApiControllerSQL::DIMENSION_WIKI_IMAGES, __METHOD__ );
		$result = [];
		while ( $row = $db->fetchObject( $rows ) ) {
			$result[] = [
				'name' => $row->image_name,
				'user_id' => $row->user_id,
				'minor_mime' => $row->minor_mime,
				'media_type' => $row->media_type,
				'created_at' => $row->created_at
			];
		}
		$db->freeResult( $rows );

		return $result;
	}

	public function getWikiInfo() {
		$this->getDataPerWiki( array( $this, 'getWikiInfoData' ) );
	}

	private function getWikiInfoData( $db ) {
		$rows = $db->query( DWDimensionApiControllerSQL::DIMENSION_WIKI_INFO, __METHOD__ );
		$result = [];
		while ( $row = $db->fetchObject( $rows ) ) {
			$result[] = [
				'total_edits' => $row->total_edits,
				'good_articles' => $row->good_articles,
				'total_pages' => $row->total_pages,
				'users' => $row->users,
				'active_users' => $row->active_users,
				'admins' => $row->admins,
				'images' => $row->images,
				'updated_at' => $row->updated_at
			];
		}
		$db->freeResult( $rows );

		return $result;
	}

	public function getWikiUserGroups() {
		$this->getDataPerWiki( array( $this, 'getWikiUserGroupsData' ) );
	}

	private function getWikiUserGroupsData( $db ) {
		$rows = $db->query( DWDimensionApiControllerSQL::DIMENSION_WIKI_USER_GROUPS, __METHOD__ );
		$result = [];
		while ( $row = $db->fetchObject( $rows ) ) {
			$result[] = [
				'user_id' => $row->user_id,
				'user_group' => $row->user_group
			];
		}
		$db->freeResult( $rows );

		return $result;
	}

	private function getWikiConnection( $cluster, $dbname ) {
		if ( !isset( $connections[ $cluster ] ) ) {
			$connections[ $cluster ] = $db = wfGetDB( DB_SLAVE, array(), 'wikicities_'.$cluster);
		}
		$connection = $connections[ $cluster ];
		$dbname = $db->strencode( $dbname );
		$connection->query("USE `".$dbname."`",__METHOD__);

		return $connection;
	}

	private function getWikiDbNames() {
		$db = $this->getSharedDbSlave();

		$limit = min( $db->strencode( $this->getRequest()->getVal( 'wiki_limit', static::LIMIT ) ), static::LIMIT_MAX );
		$afterWikiId = $db->strencode( $this->getRequest()->getVal( 'after_wiki_id', static::WIKIS_AFTER_WIKI_ID ) );

		$rows = $db->select(
			[ "city_list" ],
			[ "city_id", "city_cluster", "city_dbname" ],
			[ "city_id > ".$afterWikiId ],
			__METHOD__,
			[
				'ORDER BY' => 'city_id',
				'LIMIT' => $limit
			]
		);

		$wikis = [];
		foreach( $rows as $row ) {
			$wikis[] = [
				'wiki_id' => $row->city_id,
				'cluster' => $row->city_cluster,
				'dbname' => $row->city_dbname ];
		}
		$db->freeResult( $rows );
		$db->close();

		return $wikis;
	}

	private function getDataPerWiki( callable $dataGatherer ) {

		$wikis = $this->getWikiDbNames();

		$result = [];
		foreach( $wikis as $wiki) {
			$db = $this->getWikiConnection( $wiki[ 'cluster' ], $wiki[ 'dbname' ] );
			$sub_result = call_user_func( $dataGatherer, $db );
			$result[] = [
				'wiki_id' => $wiki[ 'wiki_id' ],
				'data' => $sub_result
			];
		}
		foreach( $this->connections as $connection ) {
			$connection->close();
		}

		$this->setResponseData(
			$result,
			null,
			WikiaResponse::CACHE_DISABLED
		);
	}
}

