;(function($) {
	/**
	 * 
	 * NewRSPreviewSlider
	 * 
	 */
	function NewRSPreviewSlider( el, adminObj) {
		var self = this;
		self.admin = adminObj;
		self.templateEditor = $('#template-editor').detach().css('display', 'block');
		self.templateTextarea = self.templateEditor.find('textarea');
		self.previewContainer = $('<div id="newrs-preview"></div>');
		var editor,
			lastSize = {width: 500, height: 337 };
		$('#edit-slide-markup').click(function(e) {
			e.preventDefault();
			self.templateEditor.rsdialog({
				modal: true,
				title: newRsVars.slide_html_markup_editor,
				zIndex: 15,
				width: lastSize.width,
				height: lastSize.height,
				beforeclose: function() {
					if(editor) {
						editor = null;
					}
				},
				open: function() { 
					$(".ui-widget-overlay").unbind('click.rst').bind('click.rst', function () {
			    		 self.templateEditor.rsdialog( "close" );
					});
					$('.ui-dialog-titlebar-close')[0].tabIndex = -1;
					
					editor = window.newrsGetCodeMirror( self.templateTextarea );
					editor = editor[0].cmInstance;
					editor.setSize('100%', (lastSize.height - 35) );

				}	
			}).unbind('dialogresize').bind( "dialogresize", function( event, ui ) {
				if(editor && ui) {
					lastSize = ui.size;
					editor.setSize('100%', (lastSize.height - 35) + 'px');
				}
			});
		});

		if( $('#preview-slider').length ) {
			$('#preview-slider').click(function(e) {
				e.preventDefault();
				self.openPreview();
			});
		}		


	}


	NewRSPreviewSlider.prototype = {
		setTemplateVal: function(val) {
			this.templateTextarea.html(val).val(val);
		},
		getTemplateVal: function() {
			return this.templateTextarea.val();
		},
		openPreview: function() {
			var self = this;
			
			if(self.previewXHR) {
				self.previewXHR.abort();
			}

			var formData = self.admin.getOptions() ;


			//self.previewContainer.css('visibility', 'hidden');
			$('#preview-slider').text(newRsVars.loading_preview);
			//return;
			self.previewXHR = $.ajax({
				url: newRsVars.ajaxurl,
				type: 'post',
				data: {
					action : 'getSliderMarkup',
					markup: self.templateTextarea.val(),
					options: formData,
					slider_type: $('#admin-slider-type').val(),
					slides: self.admin.getSlidesData(),
					template: $('#template-select  input:checked').val(),
					skin: $('#skin-select').val(),
					_ajax_nonce : newRsVars.previewNonce
				},
				complete: function(data) {	
					$('.ui-dialog').css('visibility', 'hidden');
					$('#preview-slider').text(newRsVars.preview_slider);

					self.previewContainer.empty();
					var tabsContainer = self.previewContainer.rsdialog({
						modal: true,
						title: newRsVars.preview_title,
						zIndex: 15,
						width: '80%',
						height: 'auto',
						beforeclose: function() {
							if(self.slider && self.slider.data('royalSlider')) {
								self.slider.data('royalSlider').destroy(true);
							}
						},
						open: function() { 
							$(".ui-widget-overlay").unbind('click.rst').bind('click.rst', function () {
					    		 tabsContainer.rsdialog( "close" );
							});
						}
					});

					self.previewContainer.html(data.responseText);
					self.slider = self.previewContainer.find('.royalSlider');

					if(self.slider.length && self.slider.hasClass('royalSlider')) {
						
						

						var dimensions = self.admin.getDimensions();
						if(dimensions.width.indexOf('%') !== -1) {
							tabsContainer.rsdialog( "option", "width", '50%');
						} else {
							tabsContainer.rsdialog( "option", "width", 'auto');
						}
						self.slider.css(dimensions);
						//tabsContainer.rsdialog('option', 'position', 'center');

						setTimeout(function() {
							var sliderOpts = formData.sopts;
							delete formData.sopts;
							sliderOpts = $.extend(sliderOpts, formData);


							var slider = self.slider.royalSlider( sliderOpts ).data('royalSlider');

							slider.ev.on('rsEnterFullscreen', function() {
								$('#wpadminbar').css('visibility', 'hidden');
							});
							slider.ev.on('rsExitFullscreen', function() {
								$('#wpadminbar').css('visibility', 'visible');
							});
							setTimeout(function() {
								tabsContainer.rsdialog('option', 'position', 'center');
								setTimeout(function() {
									$('.ui-dialog').css('visibility', 'visible');
								}, 16);
							}, 16);
							
						}, 1);
					} else {
						tabsContainer.rsdialog( "option", "width", '400px');
						tabsContainer.rsdialog('option', 'position', 'center');
						$('.ui-dialog').css('visibility', 'visible');
						self.previewContainer.html(newRsVars.unexpected_output + '<br/>------------<br/>' + data.responseText);
					}

					
					

				},
			    error: function(jqXHR, textStatus, errorThrown) { $('.ui-dialog').css('visibility', 'visible'); $('#preview-slider').text(newRsVars.preview_slider); }
			});

		}
		
	};



	$.fn.newRSPreviewSlider = function(admin) {    
		return this.each(function(){
			var self = $(this);
			var o = new NewRSPreviewSlider(self, admin);
			$(this).data('newRSPreviewSlider', o);
		});
	};
})(jQuery);