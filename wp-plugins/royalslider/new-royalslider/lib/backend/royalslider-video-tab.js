;(function($) {
	/**
	 * 
	 * NewRSVideoSelect
	 * 
	 */
	function NewRSVideoSelect(element, admin) {
		var self = this;
		var videoTab = self.el = element;
		self.admin = admin;

		var input = self.input = videoTab.find('[name="slides[video][url]"]');
		self.status = newRsVars.supports_video;
		self.urlstatus = $('<div class="video-url-status status-text"></div>').appendTo(element);
		self.currUrl = '';

		self.currTitle = '';
		self.currDesc = '';

		//self.vTitle = element.find('input[name="slides[video][title]"]');
		//self.vDescription = element.find('textarea');
		
		var timeout;
		
		input.bind('textchange.rsvt', function() {
			if(timeout) clearTimeout(timeout);
			timeout = setTimeout(function () {

				self.updateVideoData(self, input.val());
			}, 600);
		}).bind('change.rsvt', function() {
			if(timeout) clearTimeout(timeout);
			self.updateVideoData(self, input.val());
		});
		self.urlstatus.delegate('button', 'click', function() {
			self.admin.setTitleCaption(self.currTitle, self.currDesc, true);
			$(this).remove();
		});
			

		
				

			


	}


	NewRSVideoSelect.prototype = {
		updateVideoData: function(self, url, onChange) {
			var match,
				regExp,
				type,
				videoId;
			if(url) {
				type = '';
				videoId = '';
				if( url.match(/youtu\.be/i) || url.match(/youtube\.com/i) ) {
					regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
				    match = url.match(regExp);
				    if (match && match[7].length==11){
				        videoId = match[7];
				    }
				    type = 'YouTube';
				} else if(url.match(/vimeo\.com/i)) {
					regExp = /(www\.)?vimeo.com\/(\d+)($|\/)/;
					match = url.match(regExp);
					if(match) {
						videoId = match[2];
					}
					type = 'Vimeo';
				}

				if(type && !videoId) {
					self.setStatus( newRsVars.incorrect_x_video_url.replace('%s',type) );
				} else if(!type && !videoId) {
					self.setStatus( newRsVars.incorrect_video_url );
				} else if(type && videoId) {
					newStatus = type + ' : ' + videoId;
					self.getImageData(type, videoId);
				}
			} else {
				self.setStatus( '' );
				self.admin.setVideoImage('','');
			}
			
			
		},
		getImageData: function(type, videoId) {
			var self = this;
			var isVimeo = Boolean(type === 'Vimeo');
			var url = isVimeo ? 'http://vimeo.com/api/v2/video/'+videoId+'.json?callback=?' : 'http://gdata.youtube.com/feeds/api/videos/'+videoId+'?v=2&alt=jsonc';

			if(url === self.currUrl) {
				return;
			}

			if(self.currRequest){
				self.currRequest.abort();
			}


			self.requestTimeout = setTimeout(function() {
				self.setStatus( newRsVars.incorrect_id_url );
				self.currUrl = '';
			}, 12000);

			var img,
				thumb,
				title,
				description;

			self.setStatus( newRsVars.fetching_video_data );
			self.currUrl = url;
			self.currRequest = $.getJSON(
				url,
				function(data) {
					if(isVimeo) {
						data = data[0];
						img = data.thumbnail_large;
						thumb = data.thumbnail_small;
						title = data.title;
						description = data.description;
					} else {
						data = data.data;
						img = data.thumbnail.hqDefault;
						thumb = data.thumbnail.sqDefault;
						title = data.title;
						description = data.description;
					}

					var completeStatus = newRsVars.found_video + ' <strong>"' + title+ '"</strong>'; 
					if( !self.setVideoImagePaths(img, thumb, title, description) ) {
						completeStatus += ' <button class="button ">' + newRsVars.fetch_title_description + '</button>';
					}
					self.setStatus( completeStatus )
					self.currRequest = null;
					clearTimeout(self.requestTimeout);
					self.requestTimeout = null;
				}
			).error(function() {
				self.currUrl = '';
				self.currRequest = null;
				self.onVideoDataLoadError(type);
				clearTimeout(self.requestTimeout);
				self.requestTimeout = null;
			});


		},
		setStatus: function(newStatus) {
			var self = this;
			if(newStatus !== self.status) {
				self.status = newStatus;
				self.urlstatus.html(self.status);
			}
		},
		destroy: function() {
			var self = this;
			self.input.unbind('textchange.rsvt change.rsvt');
			self.urlstatus.undelegate('button', 'click');
			self.urlstatus.remove();
		},
		onVideoDataLoadError: function(type) {
			var self = this;
			self.setStatus( newRsVars.incorrect_x_video_url.replace('%s',type) );
		},
		setVideoImagePaths: function(img, thumb, title, description) {
			var self = this;

			self.currTitle = title;
			self.currDesc = description;
			self.admin.setVideoImage(img, thumb);
			return self.admin.setTitleCaption(title, description);

			//self.setStatus('');
			//ideoDataHolder.empty();
			
			//videoDataHolder.append('<img src="'+img+'" />');
			// videoDataHolder.append('<img src="'+thumb+'" />');
			////self.vTitle.val(title);
			//self.vDescription.html(description);
		}
	};



	$.fn.newRSVideoSelect = function(admin) {    
		return this.each(function(){
			var self = $(this);
			var o = new NewRSVideoSelect(self, admin);
			$(this).data('newRSVideoSelect', o);
		});
	};
})(jQuery);