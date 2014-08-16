
;(function($) {
	/**
	 * 
	 * dsframework.MediaManager 2.0
	 * Extends default WordPress media popup.
	 * 
	 */
	function MediaManager(element, options) {
		var self = this;
		self.isSingle = rsMediaAddVars.isSingle;
		
		self.isMedia = false;
		self.win = parent || top;


		var location = document.location.search.match(/tab\=([a-zA-Z0-9\-_]+)/);
		if(location) {
			if(location[1] === 'type') {
				self.parseTab();
			} else {
				self.isMedia = true;
				self.parseTab(true);
			}
		} else {
			self.parseTab();
		}

	} /* constructor end */
	
	MediaManager.prototype = {
		
		parseTab: function(isMedia) {
			var self = this;
			//var mediaItems = $('#media-items');
			// setInterval(function() {
			// 	if(!$('#media-items').hasClass('new-rs-media')) {
			// 		self.addButtons(isMedia);
			// 	}
			// }, 3000);
			self.addButtons(isMedia);
		},
		addButtons: function(isMedia) {
			var self = this,
				btn,
				label,
				onSingleButtonClick = function(e) {
					e.preventDefault();
					var id = $(this).closest('.media-item').find('.media-item-info').attr('id');
					var btn = $(this);
					self.setButtonText( btn, rsMediaAddVars.adding );
					
					self.sendToServer( [parseInt(id.substring(11))], btn );
				};
			if(isMedia) {
				$('#filter').append('<input type="hidden" name="newrs-a-gallery-enabled" value="1" />');
				// move add button
				$('#media-upload .media-item').each(function() {
					$(this).prepend( $(this).find('.dsframework-tb-add-image-button').click(onSingleButtonClick) );
				});

				// rename Add to Post
				$('#media-items .media-item input[type="submit"]').val( rsMediaAddVars.add_to_slider ).click(onSingleButtonClick);

				// add Insert All
				if(!self.isSingle) {
					var l = $('#media-items .media-item').length;
					if(l > 0) {
						label =  (l > 1) ? (rsMediaAddVars.add + l + rsMediaAddVars.images_to_slider) : rsMediaAddVars.add_to_slider_singular;
					} else {
						lable = 'You don\'t have any images. Upload them first.';
					}
					
				}

				
			} else {
				// rename Add to Post
				$("#media-items").delegate('.media-item', 'mouseenter', function(e) {
					$(this).find('input[type="submit"]').not('.has-event').addClass('has-event').val( rsMediaAddVars.add_to_slider ).click(onSingleButtonClick);
				})
				label = rsMediaAddVars.add_all_uploaded_to_slider;
			}

			if(!self.isSingle) {
				btn = $('<button id="rs-add-all-images" class="button button-primary">'+label+'</button>');
				btn.appendTo('form .ml-submit').click(function(e){
		            e.preventDefault();
		            
		            var attachments = [];
		            $('#media-items .media-item').each(function(){
		            	var id = $(this).find('.media-item-info').attr('id');
		            	attachments.push( parseInt(id.substring(11)) );
		            });
		            self.sendToServer( attachments );
		        });
			}
		},
		setButtonText: function (btn, text) {
			if(btn.is('button')) {
				btn.text(text);
			} else {
				btn.val(text);
			}
		},
		sendToServer: function(attachments, btn) {
			var self = this,
				win = self.win;

			var isSingle = self.isSingle;
			if(!btn) {
				self.setButtonText( $('#rs-add-all-images'), rsMediaAddVars.adding );
			}

			$.ajax({
				url: rsMediaAddVars.ajaxurl,
				type: 'post',
				data: {
					action : isSingle ? 'newRsSingleMedia' : 'newRsCustomMedia',
					_ajax_nonce: (isSingle ? rsMediaAddVars.getSingleImageNonce : rsMediaAddVars.getImagesNonce),
					attachments: attachments
				},
				complete: function(data) {			
					if(!isSingle) {
						win.rsAdminGlobal.addItems( data.responseText );
						if(btn) {
							self.setButtonText( btn, rsMediaAddVars.added );
						} else {
							win.tb_remove();
						}
					} else {
						win.rsAdminGlobal.singleImageSelect( $.parseJSON(data.responseText) );
						win.tb_remove();
					}
				},
			    error: function(jqXHR, textStatus, errorThrown) { self.setButtonText( $('#rs-add-all-images'), 'There was a problem with request, try again please.' ); }
			});

		},

	}; /* prototype end */

	$.fn.mediaManager = function(options) {    	
		return this.each(function(){
			var mediaManager = new MediaManager($(this), options);
			$(this).data('mediaManager', mediaManager);
		});
	};
})(jQuery);

jQuery(document).ready(function($) {
	if( !$('#new_royalslider_media_library').length ) {
		return;
	}
	$(document).mediaManager();
});