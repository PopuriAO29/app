var WikiaMobile = WikiaMobile || (function() {
	/** @private **/

	var body,
	allImages = [],
	handledTables,
	deviceWidth = ($.os.ios) ? 268 : 300,
	deviceHeight = ($.os.ios) ? 416 : 513,
	//realWidth = ($.os.ios) ? ((window.orientation == 0) ? screen.width : screen.height) : screen.width,
	//realHeight = ($.os.ios) ? ((window.orientation == 0) ? screen.height : screen.width) : screen.height,
	realWidth = window.innerWidth || window.clientWidth,
	realHeight = window.innerHeight || window.clientHeight,
	//TODO: finalize the following line and update all references to it (also in extensions)
	clickEvent = ('ontap' in window) ? 'tap' : 'click',
	touchEvent = ('ontouchstart' in window) ? 'touchstart' : 'mousedown',
	sizeEvent = ('onorientationchange' in window) ? 'orientationchange' : 'resize';

	function getImages(){
		return allImages;
	}

	//slide up the addressbar on webkit mobile browsers for maximum reading area
	//setTimeout is necessary to make it work on ios...
	function hideURLBar(){
		setTimeout(function(){
			if(!window.pageYOffset)
				window.scrollTo(0, 1);
		}, 1);
	}

	function processTables(){
		if(typeof handledTables == 'undefined'){
			handledTables = [];
			
			$('table').not('table table').each(function(){
				var table = $(this),
				rows = table.find('tr'),
				rowsLength = rows.length;

				//handle custom and standard infoboxes
				if(table.hasClass('infobox'))
					return true;
				
				//find infobox like tables
				if(rowsLength > 2){
					var correctRows = 0,
					cellLength;

					$.each(rows, function(index, row) {
						cellLength = row.cells.length;
						
						if(cellLength > 2)
							return false;
						
						if(cellLength == 2)
							correctRows++;
						
						//sample only the first X rows
						if(index == 9)
							return false;
					});

					if(correctRows > Math.floor(rowsLength/2)) {
						table.addClass('infobox');
						return true;
					}
				}

				//if the table width is bigger than any screen dimension (device can rotate)
				//or taller than the allowed vertical size, then wrap it and/or add it to
				//the list of handled tables for speeding up successive calls
				//NOTE: tables with 100% width have the same width of the screen, check the size of the first row instead
				var firstRowWidth = rows.first().width(),
					tableHeight = table.height();

				table.data('width', firstRowWidth);
				table.data('height', tableHeight);

				if(firstRowWidth > realWidth || table.height() > deviceWidth){
					table.wrapAll('<div class="bigTable">');
					handledTables.push(table);
				} else if(firstRowWidth > realHeight)
					handledTables.push(table);
			});

			if(handledTables.length > 0)
				window.addEventListener(sizeEvent, processTables);
		}else if(handledTables.length > 0){
			var table, row, isWrapped, isBig,
				maxWidth = window.innerWidth || window.clientWidth;

			for(var x = 0, y = handledTables.length; x < y; x++){
				table = handledTables[x];
				row = table.find('tr').first();
				isWrapped = table.parent().hasClass('bigTable');
				isBig = (table.data('width') > maxWidth || table.data('height') > deviceWidth);

				if(!isWrapped && isBig)
					table.wrap('<div class="bigTable">');
				else if(isWrapped && !isBig)
					table.unwrap();
			}
		}
	}

	function processImages(){
		var number = 0,
		image;

		$('.infobox .image').each(function(){
			allImages.push([$(this).data('number', number++).attr('href')]);
		});

		$('figure').each(function(){
			var self = $(this);
			allImages.push([
				self.find('.image').data('number', number++).attr('href'),
				self.find('.thumbcaption').html()
			]);
		});

		$('.wikia-slideshow').each(function(){
			var slideshow = $(this),
			length = slideshow.data('number', number++).data('image-count');

			for(var i = 0; i < length; i++) {
				allImages.push([slideshow.data('slideshow-image-id-' + i)]);
			}
		});

		//if there is only one image in the article hide the prev/next buttons
		//in the image modal
		//TODO: move to a modal API call
		if(allImages.length <= 1) $('body').addClass('justOneImage');
	}

	function getDeviceResolution(){
		return [deviceWidth, deviceHeight];
	}

	function imgModal(number, caption){
		$.openModal({
			imageNumber: number,
			toHide: '.changeImageButton',
			caption: caption,
			addClass: 'imageModal'
		});
	}

	function getClickEvent(){
		return clickEvent;
	}

	function getTouchEvent(){
		return touchEvent;
	}

	function track(ev){
		WikiaTracker.track('/1_mobile/' + ((ev instanceof Array) ? ev.join('/') : ev), 'main.sampled');
	}

	//init
	$(function(){
		body = $(document.body);
		var navigationWordMark = $('#navigationWordMark'),
		navigationSearch = $('#navigationSearch'),
		searchToggle = $('#searchToggle'),
		searchInput = $('#searchInput'),
		wikiaAdPlace = $('#WikiaAdPlace');

		//analytics
		track('view');

		processTables();
		//add class to collapse section as quick as possible,
		//must be done AFTER detecting size of elements on the page
		body.addClass('js');

		hideURLBar();
		processImages();

		//TODO: optimize selectors caching for this file
		body.delegate('.collapsible-section', clickEvent, function(){
			var self = $(this);

			track(['section', self.hasClass('open') ? 'close' : 'open']);
			self.toggleClass('open').next().toggleClass('open');
		})
		.delegate('#WikiaMainContent a', clickEvent, function(){
			track(['link', 'content']);
		})
		.delegate('#WikiaArticleCategories a', clickEvent, function(){
			track(['link', 'category']);
		})
		.delegate('.infobox img', clickEvent, function(event){
			event.preventDefault();
			imgModal($(this).parents('.image').data('number'));
		})
		.delegate('figure', clickEvent, function(event){
			event.preventDefault();

			var thumb = $(this),
			image = thumb.children('.image').first();
			imgModal(image.data('number'), thumb.children('.thumbcaption').html());
		})
		.delegate('.wikia-slideshow', clickEvent, function(event){
			event.preventDefault();
			imgModal($(this).data('number'));
		})
		.delegate('.bigTable', clickEvent, function(event){
			event.preventDefault();

			$.openModal({
				addClass: 'wideTable',
				html: this.innerHTML
			});
		});

		$('#searchForm').bind('submit', function(){
			track(['search', 'submit']);
		});

		$('#searchToggle').bind(clickEvent, function(event){
			var self = $(this);

			if(self.hasClass('open')){
				track(['search', 'toggle', 'close']);
				navigationWordMark.show();
				navigationSearch.hide().removeClass('open');
				self.removeClass('open');
				searchInput.val('');
			}else{
				track(['search', 'toggle', 'open']);
				navigationWordMark.hide();
				navigationSearch.show().addClass('open');
				self.addClass('open');
			}
		});

		$('#WikiaPage').bind(clickEvent, function(event){
			navigationWordMark.show();
			navigationSearch.hide().removeClass('open');
			searchToggle.removeClass('open');
			searchInput.val('');
		});

		$('#fullSiteSwitch').bind(clickEvent, function(event){
			event.preventDefault();

			track(['link', 'fullsite']);
			Wikia.CookieCutter.set('mobilefullsite', 'true');
			location.reload();
		});
	});

	return {
		getImages: getImages,
		getDeviceResolution: getDeviceResolution,
		getClickEvent: getClickEvent,
		getTouchEvent: getTouchEvent,
		track: track
	}
})();