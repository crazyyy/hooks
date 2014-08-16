(function($) {
  $.fn.outerHTML = function() {
    return $(this).clone().wrap('<div></div>').parent().html();
  }
  $.fn.rsGetVal = function(){
	    var el = $(this);
    	if (el.attr('type') === 'checkbox') {
	        return el.is(":checked") ? el.val() : false;
	    } else {
	        return el.val();
	    }
	   
	};
  $.fn.rsSetVal = function(value){
	    var el = $(this);
	    

    	if (el.attr('type') === 'checkbox') {
    		if(value && value !== 'false') {
    			return el.attr('checked', 'checked');
    		} else {
    			return el.removeAttr('checked');
    		}
	        
	    } else {
	        return el.val(value);
	    }
	    
	   
	};
	$.widget("ui.rsdialog", $.ui.dialog, {
  	_title: function( title ) {
		if ( !this.options.title ) {
			title.html("&#160;");
		}
		title.html( this.options.title );
	}
});

})(jQuery);



// Textarea and select clone() bug workaround | Spencer Tipping
// Licensed under the terms of the MIT source code license
(function (original) {
  jQuery.fn.clone = function () {
    var result           = original.apply(this, arguments),
        my_textareas     = this.find('textarea').add(this.filter('textarea')),
        result_textareas = result.find('textarea').add(result.filter('textarea')),
        my_selects       = this.find('select').add(this.filter('select')),
        result_selects   = result.find('select').add(result.filter('select'));

    for (var i = 0, l = my_textareas.length; i < l; ++i) jQuery(result_textareas[i]).val(jQuery(my_textareas[i]).val());
    for (var i = 0, l = my_selects.length;   i < l; ++i) result_selects[i].selectedIndex = my_selects[i].selectedIndex;

    return result;
  };
}) (jQuery.fn.clone);

String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}
;(function($) {
	/**
	 * 
	 * RoyalSlider edit slide admin 2.0
	 * 
	 */
	function NewRSAdmin(element, options) {
		var self = this;
		window.newRsAdmin = self;

		// long story
		var webkit = /WebKit\//.test(navigator.userAgent);
		if(webkit) {
			$('.wp-admin').addClass('rschrome');
		}

		window.newrsGetCodeMirror = function(textarea, removeLineNumbers, theme) {

			if(!window.newrsCodeMirror) {
				CodeMirror.defineMode("mustache", function(config, parserConfig) {
				  var mustacheOverlay = {
				    token: function(stream, state) {
				      var ch;
				      if (stream.match("{{")) {
				        while ((ch = stream.next()) != null)
				          if (ch == "}" && stream.next() == "}") break;
				        stream.eat("}");
				        return "mustache";
				      }
				      while (stream.next() != null && !stream.match("{{", false)) {}
				      return null;
				    }
				  };
				  return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || "text/html"), mustacheOverlay);
				});

				window.newrsCodeMirror = $('<div class="newrsCmEditor"></div>');
				window.newrsCodeMirror[0].cmInstance = CodeMirror(window.newrsCodeMirror[0], {
					lineNumbers: !removeLineNumbers,
			        theme: !theme ? 'lesser-dark' : theme,
			        mode: "mustache",
			        tabSize: 2
				});


			} else {
				if(window.newrsCodeMirror[0].textarea) {
					window.newrsCodeMirror[0].textarea.parent().find('.newrsCmEditor').remove();
					window.newrsCodeMirror[0].textarea = null;
				}
				window.newrsCodeMirror[0].cmInstance.setOption('theme', !theme ? 'lesser-dark' : theme);
				window.newrsCodeMirror[0].cmInstance.setOption('lineNumbers', !removeLineNumbers);

			}

			if(textarea) {
				textarea.css('display', 'none').parent().append( $(window.newrsCodeMirror) );
				//var parent = textarea.css('display', 'none').parent();
				//parent.append()
				
				window.newrsCodeMirror[0].cmInstance.setOption('onChange', null);
				window.newrsCodeMirror[0].textarea = textarea;
				window.newrsCodeMirror[0].cmInstance.setValue( textarea.val() );
				window.newrsCodeMirror[0].cmInstance.setOption('onChange', function(editor) {
		        	var val = window.newrsCodeMirror[0].cmInstance.getValue();
		        	window.newrsCodeMirror[0].textarea.html(val);
		        	window.newrsCodeMirror[0].textarea.val(val);
		        });
			} else {
				window.newrsCodeMirror[0].cmInstance.setOption('onChange', null);
			}
			return window.newrsCodeMirror;



		};

		// TODO
		// if(  $('#rs-flickr-options').length ) {
		// 	var ftimeout = 0;
		// 	$('#flickr-username').bind('textchange', function() {
		// 		clearTimeout(ftimeout);
		// 		ftimeout = setTimeout(function() {
		// 			$.ajax({
		// 				url: newRsVars.ajaxurl,
		// 				type: 'post',
		// 				data: {
		// 					action : 'newGetFlickrPhotosetsList',
		// 					_ajax_nonce : newRsVars.createAdminSlideNonce
		// 				},
		// 				complete: function(data) {			
		// 					self.addItems(data.responseText)
		// 					$('#create-new-slide').text(newRsVars.create_new_slide);
		// 					if( !self.editOpen) {
		// 						self.editItem( self.slidesContainer.children().last() );
		// 					}
							

		// 				},
		// 			    error: function(jqXHR, textStatus, errorThrown) { alert(textStatus); $('#create-new-slide').text(newRsVars.create_new_slide); }
		// 			});
		// 		}, 500);
		// 	});
		// }


		var postSource = $('#rs-postssource-options');
		if(postSource.length) {
			self.isPosts = true;
			self.setupPostsSource(postSource);
		}

		self.singleSelectCallback;
		self.singleImageSelect = function(response) {
			if(self.singleSelectCallback) {
				self.singleSelectCallback.call(self, response);
				self.singleSelectCallback = null;
			}
			
		};

		$('#create-new-slide').click(function(e) {
			e.preventDefault();
			self.createNewSlide();
		});

		self.manageSlidersTable = $('#new-royalslider-manage-table');
		if(self.manageSlidersTable.length) {
			self.setupManageSlidersPage();
			return;
		}

		self.editSliderText = $('#edit-slider-text span');
		self.sliderOptions = $('#new-royalslider-options .other-options');
		var isAjaxRunning = false;
		var saveButton = self.saveButton = $("#save-slider");
		self.addNewSlideButton = $('#add-new-slide-button');
		window.rsGlobalVars = {};
		self.slidesContainer = window.rsGlobalVars.slidesContainer = $('#new-rs-slides');
		self.supportsHTML5Drag = ('draggable' in document.createElement('span'));
		self.noSlidesText = $('#rs-no-slides-block');
		if(!self.getSlides().length) {
			self.noSlidesText.show();
		}

		window.rsGlobalVars.highestId = 1;
		self.refreshItems();
		// not supported, yet
		//self.bindDropFromAnotherWindow();
		self.addItemActionButtonsEvents();
    	self.initSortable();
		
		
		//self.lastOpen = $('#new-royalslider-options').not('.closed');
		$('#new-royalslider-options').delegate(".postbox h3, .postbox .handlediv","click.postboxes", function () {
			$('#new-royalslider-options').find('.postbox').addClass('closed').removeClass('open');
            self.lastOpen = $(this).parent(".postbox").addClass("open").removeClass('closed');
        });


        

        $('.rs-embed-to-site').click(function(e) {
            e.preventDefault();

            var dialog = $('#embed-info').rsdialog({
                modal: true,
                title: "",
                zIndex: 15,
                width: 500,
                resizable: false,
                height: 'auto',
                beforeClose: function() {

                },
                open: function() { 
                    $(".ui-widget-overlay").unbind('click.rst').bind('click.rst', function () {
                         dialog.rsdialog( "close" );
                    });
                }
            });
        });

		//self.sliderOptions.find('.postbox:not(:eq(0))').toggleClass('closed');
		
		// self.sliderOptions.accordion({
		// 	heightStyle: "content",
		// 	collapsible: true,
		// 	autoHeight: true
		// });
		saveButton.click(function(e) {
			e.preventDefault();
			self.saveSlider();
		});

		self.templateChanging = false;
		$('#template-select :radio').click(function(e) {
			if(self.templateChanging) {
				return false;
			}
			//e.preventDefault();
			//e.preventDefault();
			var warning = newRsVars.change_template_warning;
			//var row = $(this).parents('tr');
			//warning = warning.replace('%s', '"'+row.find('strong a').text()+'" (id #'+row.find('td').eq(0).text()+')');
			if(confirm(warning)){
			    self.refreshTemplate( $(e.target).val() );
			} else {
				return false;
			}
		});
		// First selector is the parent element, selector two is the elements which the qtips should be bound to
		$('#new-royalslider-options').delegate('.rs-opt, .rs-help-el', 'mouseover', function(event) {
			if($(this).attr('data-help')) {
				$(this).qtip({
					overwrite: false,
					content: {
						attr: 'data-help'
					},
					position: {
						at: 'center left', 
						my: 'center right'
					},
					style: {
						classes: 'ui-tooltip-rounded ui-tooltip-shadow ui-tooltip-tipsy rs-tooltip'
					},
					show: {
						event: event.type,
						ready: true,
						effect: false
					},
					hide: {
						effect: false
					}
				});
			}
		});


		self.setupOptionsJS();

		// self.sliderOptions.find('.rs-opt').each( function( ) {
		// 	var help = $(this).attr( 'data-help' );
		// 	if ( help != undefined && help != '' ) {
		// 		$(this).qtip({
		// 			content: {
		// 				attr: 'data-help'
		// 			},
		// 			position: {
		// 				at: 'center left', 
		// 				my: 'center right'
		// 			},
		// 			style: {
		// 				classes: 'ui-tooltip-rounded ui-tooltip-shadow ui-tooltip-tipsy rs-tooltip'
		// 			}
		// 		});
  //           }
		// });	

		self.generator = $(this).newRSPreviewSlider( self ).data('newRSPreviewSlider');
	 /*
	 rsgallery_globals => dsframework_global_vars
	 rsgallery => newrs-a-gallery
	  */
	 	$(document).on("click", "#add-images", function() { 
	 		self.showThickbox('media-upload.php?type=image&post_id=&newrs-a-gallery-enabled=true&TB_iframe=true');
			return false;
	 	});

		

		$('#new_royalslider_add_shortcode').submit(function (e){
			var shortcode_atts = ' post_attachments="true"';
			var shortcode = '[new_royalslider' + shortcode_atts + '] ';
			var win = window.dialogArguments || opener || parent || top;
			win.send_to_editor( shortcode );
			return false;
		});

		var prevValue = '';
		$('#block-classes-select').change(function(e) {
			var item = $(this);

			if(item.val() === 'rs_add_user_class') {
				var new_css_class = prompt(newRsVars.add_anim_block_class_prompt);
    			if(!new_css_class.length) {
    				item.val(prevValue);
    				return false;
    			} 

    			
			    item.children().eq(item.children().length - 3).after('<option val="'+new_css_class+'">' + new_css_class + '</option>');
			    item.val(new_css_class);

			    $.ajax({
					url: newRsVars.ajaxurl,
					type: 'post',
					data: {
						action : 'addAnimBlockClass',
						classToAdd : new_css_class,
						_ajax_nonce : newRsVars.addAnimBlockClassNonce
					}
				});
				e.stopImmediatePropagation();
			} else if(item.val() === 'rs_remove_user_class') {

				var new_css_class = prompt(newRsVars.remove_anim_block_class_prompt);
				item.val(prevValue);
    			if(!new_css_class.length) {
    				return false;
    			} 

    			
			    item.children('[value="' + new_css_class + '"]').remove();//eq(item.children().length - 3).after('<option val="'+new_css_class+'">' + new_css_class + '</option>');
			   // item.val(new_css_class);

			    $.ajax({
					url: newRsVars.ajaxurl,
					type: 'post',
					data: {
						action : 'addAnimBlockClass',
						classToRemove : new_css_class,
						_ajax_nonce : newRsVars.addAnimBlockClassNonce
					}
				});
				e.stopImmediatePropagation();
				

				// remove_anim_block_class_prompt
			}
			prevValue = item.val();
		});
		

	} /* constructor end */

	
	NewRSAdmin.prototype = {
		getDimensions: function() {
			var self = this;
			return {
				width: self.getOpt('width'),
				height: self.getOpt('height')
			};
		},

		getOpt: function(optname, section) {
			var self = this;
			var el = self.sliderOptions.find('[name="' + (!section ? 'sopts' : section) + '['+ optname +']"]').parent();
			
			var type = el.data('type');
			var input = el.find(':input');
			var v;
			if(input.is(':checkbox')) {
				v = input.is(':checked');
			} else {
				v = input.val();
			}

			function getFormattedVar(v, type) {
            	if(!type || typeof v !== 'string') return v;
            	switch( type.toLowerCase() ) {
	            	case 'str':
	            		return v.toString();
	            	break;
	            	case 'num':
	            		return parseFloat(v);
	            	break;
	            	case 'int':
	            		return parseInt(v, 10);
	            	break;
	            	case 'bool':
	            		v = v.toLowerCase();
	            		return (v === '' || v === '1' || v === 'on' || v === 'true' || v === 'yes') ? true : false;
	            	break;
	            }
            }

			return getFormattedVar(v, type); 
		},
		
		addItems: function(html) {
			var self = this;
			self.slidesContainer.append(html);
			self.refreshItems();
			self.noSlidesText.hide();
		},
		refreshItems: function() {
			var self = this;
			self.getSlides().unbind('dragstart.rsdg').bind('dragstart.rsdg', function(e) {
	            var dt = e.originalEvent.dataTransfer;
	            dt.setData("rsSlide", $(this).outerHTML() );
        	});
		},
		initSortable: function() {
			var self = this;
			var opts = {
				placeholder: 'sortable-placeholder',
				items: '.rsSlideItem',
				helper : 'clone',
				delay: 50
			};

			// if(self.supportsHTML5Drag) {
			// 	self.slidesContainer.html5Sortable('destroy');
			// 	self.slidesContainer.html5Sortable(opts);
			// } else {
				self.slidesContainer.sortable(opts);
			//}
			
		},
		getSlides: function() {
			return this.slidesContainer.find('.rsSlideItem');
		},
		removeItem: function(item) {
			item.remove();
			var self = this;
			if(!self.getSlides().length) {
				self.noSlidesText.show();
			}
		},

		duplicateItem: function(item) {
			var self = this;
			item.after( item.clone() );
			self.refreshItems();
		},
		editItem: function(item) {
			var self = this;
			var startTab = 0;
			var tabs = item.find('.rs-tabs');

			var editor;
			var tabsContent = tabs.find('.rs-tabs-wrap');
			self.editOpen = true;
			//var menu = tabs.find('.rs-tabs-menu');
			var tabsData = [
				{
					name: newRsVars.tab_image_video,
					id: 'rs-image-tab'
				},
				{
					name: newRsVars.tab_block_editor,
					id: 'rs-animated-block-tab',
				},
				{
					name: newRsVars.tab_html_content,
					id: 'rs-html-tab'
				}
			];
			var tabHTML = '<ul class="rs-tabs-menu">';
			for(var i = 0; i < tabsData.length; i++) {
				var tab = tabsData[i];
				tabHTML += '<li data-selector="'+tab.id+'"><a href="#" class="in-page-action">'+tab.name+'</a></li>';
			}
			tabHTML += '</ul>';
			var menu = $(tabHTML);

			var menuItems = menu.children();
			tabsContent.children().hide();
			var arrow = $('<div class="rs-menu-arrow"><div></div></div>');

			var blockEditor;
			var videoTab;

			// if(tabs.data('ext-inited')) {
			// 	tabs.extractor('destroy');
			// }

			self.currTabsDialog = tabs.extractor({
				modal: true,
				title: menu,
				zIndex: 15,
				width: 760,
				height: 370,
				beforeClose: function() {
					onEditorClose();
				},
				open: function() { 
					$(".ui-widget-overlay").unbind('click.rst').bind('click.rst', function () {
			    		 tabs.rsdialog( "close" );
					});
					$('.ui-dialog-titlebar-close')[0].tabIndex = -1;
				}

			}).data('ext-inited', true);


			var imageTab = tabsContent.find('.rs-image-tab');
			imageTab.find('.rs-label').qtip({
				overwrite: false,
				content: {
					text: function (api) {
                        return $(this).parent().attr('data-help');
                    }
				},
				position: {
					at: 'center left', 
					my: 'center right'
				},
				style: {
					classes: 'ui-tooltip-rounded ui-tooltip-shadow ui-tooltip-tipsy rs-tooltip'
				}
			});

			self.adminThumbImg = item.find('.rsMainThumb');

			self.imageId = imageTab.find('[name="slides[image][attachment_id]"]').add( imageTab.find('[name="mediaimages[id]"]') );

			self.imageTabThumbImg = imageTab.find('img');
			self.imageTabLargeImage = imageTab.find('[ name="adminarea[large]" ]');
			self.adminLargeImage = imageTab.find('[ name="adminarea[large_gen]" ]');
			self.adminLargeImageWidth = imageTab.find('[ name="adminarea[large_width]" ]');
			self.adminLargeImageHeight = imageTab.find('[ name="adminarea[large_height]" ]');
			
			self.videoImageSrc = imageTab.find('[ name="slides[video][image]" ]');
			self.videoImageThumbSrc = imageTab.find('[ name="slides[video][thumb]" ]');

			self.imageTabTitle = imageTab.find('input[name="slides[title]"]');
			self.imageTabDescription = imageTab.find('textarea');
			self.videoSelectInput = imageTab.find('.rs-video-select').newRSVideoSelect(self);

			menuItems.click(function(e) {
				var target = $(e.target).closest('li');
				menuItems.removeClass('rs-tab-selected');
				target.addClass('rs-tab-selected');
				tabsContent.children().hide();
				tabsContent.find('.'+target.data('selector')).show();
				switch(target.data('selector')) {
					case 'rs-html-tab':
						htmlTab();
					break;
					case 'rs-video-tab':
						if(!videoTab) 
							videoTab = tabsContent.find('.rs-video-tab').newRSVideoSelect(self);
						else 
							videoTab.data('newRSVideoSelect').show();
					break;
					case 'rs-animated-block-tab':
						if(!self.blockEditor) 
							self.blockEditor = self.slidesContainer.newRSBlockEditor(self, tabsContent.find('.rs-animated-block-tab') ,tabs);
						else 
							self.blockEditor.data('newRSBlockEditor').show( tabsContent.find('.rs-animated-block-tab') );
					break;
				}
			}).eq(startTab).trigger('click');


			// image tab
			self.selectImageBtn = imageTab.find('.rs-select-image').bind('click', function(e) {
				e.preventDefault();
				self.showThickbox('media-upload.php?type=image&post_id=&newrs-a-gallery-enabled=true&newrs-a-gallery-single=true&TB_iframe=true', self.onImageSelect);
			});
			self.removeImageBtn = imageTab.find('.rs-remove-image').bind('click', function(e) {
				e.preventDefault();
				self.removeImage();
			});


			// HTML content tab
			function htmlTab(reset) {
				var htmlTab = tabsContent.find('.rs-html-tab');
				var textarea = htmlTab.find('[name="slides[html]"]');

				if(reset) {
					window.newrsGetCodeMirror(false);
					return;
				}
				var editor = window.newrsGetCodeMirror(textarea);
				editor[0].cmInstance.setSize('100%', '322px');
			}

			

			function onEditorClose() {
				self.editOpen = false;
				menuItems.unbind('click');
				imageTab.find('.rs-select-image .rs-remove-image').unbind('click');
				htmlTab(true);
				self.videoSelectInput.data('newRSVideoSelect').destroy();
				if(self.blockEditor && self.blockEditor.data('newRSBlockEditor') ) {
					self.blockEditor.data('newRSBlockEditor').hide();
				}
			}

		},
		createNewSlide: function() {
			var self = this;
			$('#create-new-slide').text(newRsVars.creating_slide);
			$.ajax({
				url: newRsVars.ajaxurl,
				type: 'post',
				data: {
					action : 'newRsCreateNewSlide',
					_ajax_nonce : newRsVars.createAdminSlideNonce
				},
				complete: function(data) {			
					self.addItems(data.responseText)
					$('#create-new-slide').text(newRsVars.create_new_slide);
					if( !self.editOpen) {
						self.editItem( self.slidesContainer.children().last() );
					}
					

				},
			    error: function(jqXHR, textStatus, errorThrown) { alert(textStatus); $('#create-new-slide').text(newRsVars.create_new_slide); }
			});
		},
		onImageSelect: function(obj) {
			var self = this;
			self.setImage(obj.id, obj.src, obj.large, obj.large_width, obj.large_height);
			// self.imageInputToSelect.val(obj.id);
			// self.imageElToSelect.attr('src', obj.src);\
			self.setTitleCaption(obj.title, obj.caption)
			
		},
		setTitleCaption: function(title, caption, force) {
			var self = this;
			if(force || !(self.imageTabTitle.val().length > 0) ) {
				self.imageTabTitle.val(title);
				self.imageTabDescription.val(caption);
				return true;
			} else {
				return false;
			}
		},
		setVideoImage: function(image, thumb) {
			var self = this;
			self.videoImageSrc.val(image);
			self.videoImageThumbSrc.val(thumb);

			if(self.imageId.val().length > 0) {

			} else {
				self.setMainThumbImg(thumb);
			}
			self.updateImageStatus();
		},
		removeImage: function() {
			var self = this;
			self.imageId.val('');

			//self.adminThumbImg.attr('src', '');
			//self.imageTabThumbImg.attr('src', '');

			self.adminLargeImage.val('');
			self.imageTabLargeImage.val('');

			self.adminLargeImageWidth.val('');
			self.adminLargeImageHeight.val('');
			self.updateImageStatus();
		},
		setImage: function(id, thumb, large, lw, lh) {
			var self = this;
			self.imageId.val(id);
			
			self.setMainThumbImg(thumb);

			self.adminLargeImage.val(large);
			self.imageTabLargeImage.val(large);

			self.adminLargeImageWidth.val(lw);
			self.adminLargeImageHeight.val(lh);
			self.updateImageStatus();
		},
		getImage: function(size) {
			var self = this;

			if(!size) {
				return self.adminLargeImage.val();
			} else if(size == 'thumb') {
				//return self.adminThumbImg.attr('src');
			} else if(size == 'id') {
				return self.imageId.val();
			} else {
				alert('getImage unknown param');
			}
		},
		updateImageStatus: function() {
			var self = this;
			var hasLibraryImage = Boolean(self.imageId.val().length > 0);
			var hasVideoImage = Boolean(self.videoImageThumbSrc.val().length > 0);

			if(!hasLibraryImage) {
				self.removeImageBtn.hide();
				self.selectImageBtn.text( hasVideoImage ? newRsVars.change_image : newRsVars.add_image);
			} else {
				self.removeImageBtn.show();
				self.selectImageBtn.text(newRsVars.change_image);
			}

			if(!hasLibraryImage) {
				var imageSrc;
				if(hasVideoImage) {
					imageSrc = self.videoImageThumbSrc.val();
				} else {
					imageSrc = newRsVars.img_folder + 'empty150.png';
				}
				self.setMainThumbImg(imageSrc);
			}

		},
		setMainThumbImg: function(src) {
			var self = this;
			self.adminThumbImg.css('background-image', 'url(\'' + src + '\')');
			self.imageTabThumbImg.attr('src', src);
		},
		getImageWidth: function() {
			return parseInt(this.adminLargeImageWidth.val(), 10);
		},
		getImageHeight: function() {
			return parseInt(this.adminLargeImageHeight.val(), 10);
		},
		addItemActionButtonsEvents: function() {
			var self = this;
			self.slidesContainer.click(function(e) {
				var target = $(e.target);

				switch(target.attr('class')) {
					case 'rs-item-action rs-remove-slide':
						e.preventDefault();
						self.removeItem(target.closest('.rsSlideItem'));
					break;
					case 'rs-item-action rs-duplicate-slide':
						e.preventDefault();
						self.duplicateItem(target.closest('.rsSlideItem'));
					break;
					case 'rsMainThumb':
					case 'rs-item-action rs-edit-slide':
						e.preventDefault();
						self.editItem(target.closest('.rsSlideItem'));
					break;
				}
			});
		},
		showThickbox: function(params, callback) {
			var self = this;
			$("#TB_window").html("");
			if(callback) {
				self.singleSelectCallback = callback;
			}
			tb_show('', params + '&width=640&height=265');
			$('#TB_title').each(function(i,item) {
				if(i !== 0) {
					$(item).remove();
				}
			});
		},
		bindDropFromAnotherWindow: function() {
			var self = this;
			var btn = $('#create-new-slide');
			btn.bind('drop', function(e) {
				btn.html( newRsVars.add_new_slide );
				var dt = e.originalEvent.dataTransfer;
	          	self.addItems(dt.getData("rsSlide"));
	            e.stopPropagation();
	            
	            return false;
	        }).bind('dragover', function(e) {
	        	
	            return false;
	        }).bind('dragenter', function(e) {
	        	btn.html( newRsVars.drop_to_duplicate );
	            return false;
	        }).bind('dragleave', function(e) {
	            btn.html( newRsVars.add_new_slide );
	            return false;
	        });
        },
        getSlidesData: function() {
        	var self = this;
        	var slides = [];
        	var items = self.getSlides().each(function(i, item) {
				var item = $(item);
				var obj = item.find(':input').toJSON(true);

				slides[i] = obj.slides ? obj.slides : {temp: true};
			});
			return slides;
        },
        getOptions: function() {
        	var self = this;
        	var obj = $('#new-royalslider-options .rs-opt, .rs-body-options .rs-opt').toJSON();

        	if(self.isPosts) {
        		obj.posts.taxonomies = jQuery('#rs-postssource-options .main-opts :checked').toJSON(true);
        	}

        	return obj;
        },
        refreshTemplate: function(templateId) {
        	var self = this,
				options = self.getOptions();

			self.templateChanging = true;

			$('#rs-template-title-text').text( newRsVars.loading_data );


        	$.ajax({
				url: newRsVars.ajaxurl,
				type: 'post',
				data: {
					templateId: templateId,
					action : 'refreshTemplate',
					type : $('#admin-slider-type').val(),
					_ajax_nonce : newRsVars.refreshTemplateNonce
				},
				complete: function(data) {		
					self.templateChanging = false;
					$('#rs-template-title-text').text( newRsVars.templates_text );

					var response = $.parseJSON(data.responseText);
					self.sliderOptions.html( response.options );
					self.generator.setTemplateVal( response.template);
					self.setupOptionsJS();
				},
			    error: function(jqXHR, textStatus, errorThrown) { alert(textStatus); self.templateChanging = false; $('#rs-template-title-text').text( newRsVars.templates_text ); }
			});
        },
        setupOptionsJS: function() {
        	var self = this;
        	var thumbOpts = self.sliderOptions.find('[name^="thumbs"]').parent();
			self.sliderOptions.find('[name="sopts[controlNavigation]"]').bind('change', function() {
				var item = $(this);
				if( $(this).val() === 'thumbnails' ) {
					thumbOpts.removeClass('ro-hidden');
				} else {
					thumbOpts.addClass('ro-hidden');
				}
			}).triggerHandler('change');


			self.sliderOptions.find('[data-child-opts]').each(function(i, el) {
				var master = $(el),
					slaves = $( '#' + master.attr('data-child-opts').split(' ').join(', #') ).parent()


				master = master.find('input:checkbox');
				master.click( function(e, custom) {
					if(custom) {
						e.preventDefault();
					}
				  if($(this).is(':checked')) {
				  	slaves.removeClass('ro-hidden');
				  } else { 
				  	slaves.addClass('ro-hidden');
				  }
				}).triggerHandler('click', 'custom');
				
			});
        },
        getTemplateId: function() {
        	return $('#dynamic-options-data').attr('data-css-class');
        },


		saveSlider: function(name, options ) {

			
			var self = this,
				name = $('#titlewrap input').val(),

				options = self.getOptions();

			if(self.saveXHR) {
				self.saveXHR.abort();
			}
			

			
			var slides = self.getSlidesData();
						
		
			self.saveButton.html( newRsVars.saving );	
			var type = $('#admin-slider-type').val();
			self.saveXHR = $.ajax({
				url: newRsVars.ajaxurl,
				type: 'post',
				data: {
					name: name,
					options: options,
					slides:slides,
					slider_id: self.saveButton.attr('data-slider-id'),
					skin: $('#skin-select').val(),
					slider_type: type,
					isCreate: (self.saveButton.attr('data-create') === 'true') ? 'true' : null,
					template: $('#template-select input:checked').val(),
					template_html: self.generator.getTemplateVal(),
					action : 'newRoyalSliderSave',
					_ajax_nonce : newRsVars.saveNonce
				},
				complete: function(data) {			

					self.saveButton.attr('data-create', 'false');
					self.saveButton.html( newRsVars.saving );
					if(data.responseText === '') {

					} else if(data.responseText === 'saved') {

					} else if( !isNaN(data.responseText) )  {
						var insertId = parseInt(data.responseText);
						self.editSliderText.html( newRsVars.edit_royalslider.replace('%s', type.capitalize() ) + insertId);
						self.saveButton.attr('data-slider-id', insertId);

						$("#embed-info code").each(function() {
						    var text = $(this).text();
						    text = text.replace("123", insertId);
						    $(this).text(text);
						});
						$("#embed-info").find('.no-id').remove();
						$('.add-new-h2.rs-hidden').removeClass('rs-hidden');
						$('.rs-embed-to-site').addClass('rs-button-glow').one('click', function() {
							$(this).removeClass('rs-button-glow');
						});

						if(history.pushState) {
							var href = window.location.href;
							var id = insertId; 
							href = href.replace('action=add', 'action=edit') + '&id='+id;
							history.replaceState({},window.title, href);
						}
						

					}
					self.saveButton.html( newRsVars.save_slider );
					setTimeout(function() {
						$('#save-progress').css({
							'opacity': 1,
							'right': self.saveButton.innerWidth() + 14
						});
						setTimeout(function() {
							$('#save-progress').css('opacity', 0);
						}, 2500);
					}, 1);
					
					// if(!(sliderID > -1)) {
					// 	$('#edit-slider-text').text('Edit RoyalSlider #' + data.responseText);							
					// }
					
					// if(parseInt(data.responseText, 10) > -1) {
					// 	sliderID = parseInt(data.responseText, 10);							
					// }
					// saveButton.html('Save Slider');
					// isUnsaved = false;
					// saveProgressButton.html('Saved').addClass('ajax-saved').removeClass('unsaved');						
					
					// isAjaxRunning = false;
				},
			    error: function(jqXHR, textStatus, errorThrown) { }
			});
			
		},
		setupManageSlidersPage: function() {
			var self= this;
			self.manageSlidersTable.find('.delete-newrslider').click(function(e) {
				e.preventDefault();
				var warning = newRsVars.delete_warning;
				var row = $(this).parents('tr');
				warning = warning.replace('%s', '"'+row.find('strong a').text()+'" (id #'+row.find('td').eq(0).text()+')');
				if(confirm(warning)){
				    window.location = $(this).data('dhref');
				}

				
			});


		},


		setupPostsSource: function(postSource) {
			postSource.on('click', '.category-tabs li a',  function(e){
            	//event.preventDefault();
            	
            	var this_id = $(this).closest('.categorydiv').attr('id'),
					taxonomyParts = this_id.split('-');
				taxonomyParts.shift();

				var taxonomy = taxonomyParts.join('-'),
					settingName = taxonomy + '_tab';


            	var t = $(this).attr('href');
				$(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
				$('#' + taxonomy + '-tabs').siblings('.tabs-panel').hide();
				$(t).show();
				return false;

				// var t = $(this).attr("href");
				// var taxonomy = $(this).parents('.categorydiv').attr('id').split('-')[1];
			 //    $(this).parent().addClass("tabs").siblings("li").removeClass("tabs");
			 //    $("#" + taxonomy + "-tabs").siblings(".tabs-panel").hide();
			 //    $(t).show();
			 //    return false;            	
            });

			function handler() {
				var this_id = $(this).closest('.categorydiv').attr('id'),
					taxonomyParts = this_id.split('-');
				taxonomyParts.shift();

				var taxonomy = taxonomyParts.join('-'),
					settingName = taxonomy + '_tab';

				var t = $(this), c = t.is(':checked'), id = t.val();
				if ( id && t.parents('#taxonomy-'+taxonomy).length )
					$('#in-' + taxonomy + '-' + id + ', #in-popular-' + taxonomy + '-' + id).prop( 'checked', c );
			}
            postSource.on('click', '.categorydiv input[type="checkbox"]', handler);
            postSource.find('.categorydiv .main-opts input[type="checkbox"]').each(handler);
			//function 

			postSource.find('#posts-post_type').change(function() {
				// To enable 

				var input = $(this).attr('disabled', 'disabled');
				if(self.postTypeXHR) {
					self.postTypeXHR.abort();
				}

				self.postTypeXHR = $.ajax({
					url: newRsVars.ajaxurl,
					type: 'post',
					data: {
						action : 'newRsGetPostTypeTerms',
						post_type : $(this).val(),
						_ajax_nonce : newRsVars.customSourceActionNonce
					},
					complete: function(data) {			
						$('#rs-taxonomies-fields').html(data.responseText);
						input.removeAttr('disabled');
					},
				    error: function(jqXHR, textStatus, errorThrown) { $('#rs-taxonomies-fields').html(); input.removeAttr('disabled'); }
				});
			});
		} 

	}; /* prototype end */

	$.fn.newRSAdmin = function(options) {    	
		return this.each(function(){
			var rsAdmin = new NewRSAdmin($(this), options);
			$(this).data('newRsAdmin', rsAdmin);
		});
	};
})(jQuery);

jQuery(document).ready(function($) {
	window.rsAdminGlobal = $(document).newRSAdmin().data('newRsAdmin');
});