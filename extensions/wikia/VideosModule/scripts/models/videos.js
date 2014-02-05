define( 'videosmodule.models.videos', [
	'wikia.nirvana'
], function( nirvana ) {
	'use strict';

	var VideosData = function( options ) {
		this.verticalOnly = options ? options.verticalOnly : null;
		this.data = null;
		this.articleId = window.wgArticleId || null;
	};

	VideosData.prototype.fetch = function() {
		var ret,
			self = this;

		if ( this.data !== null ) {
			ret = this.data;
		} else {
			ret = nirvana.getJson( 'VideosModuleController', 'index', {
				articleId: this.articleId,
				verticalOnly: this.verticalOnly
			} ).done( function( data ) {
				self.data = data;
			} );
		}

		return ret;
	};

	return VideosData;
} );
