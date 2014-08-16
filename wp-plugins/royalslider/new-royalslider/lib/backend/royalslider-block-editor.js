;(function($) {

	/**
	 * 
	 * RoyalSlider edit slide admin 2.0
	 * 
	 */
	function NewRSBlockEditor(obj, el, admin, popup) {
		

		var self = this;
		self.el = el;
		self.admin = admin;
		self.popup = popup;
		self.currView = 'desktop';
		self.ilist = ['left', 'right', 'top', 'bottom', 'width', 'height'];


		self.idatalist = ['fade-effect', 'move-effect', 'speed', 'delay', 'easing', 'move-offset'];
		self.idatalistAdmin = ['fadeEffect', 'moveEffect', 'speed', 'delay', 'easing', 'moveOffset'];
		

		self.maxItemId = 0;

		self.container = $('<div class="rs-blocks-editor"></div>');
		self.slideArea = $('<div class="rs-be-slidearea"></div>').appendTo(self.container);
		self.bgImage = $('<img class="rs-be-image" src="" />').appendTo(self.slideArea);
		self.animatedBlocks = $('<div class="rs-animated-blocks"></div>').appendTo(self.slideArea);
		

		self.controls = $('#rs-be').css('display', 'block');
		self.container.append(self.controls);
		self.currBlockId = null;
	
	
		self.controls.find('.rs-be-add-html-block').click(function() {
			self.addBlock();
		});

		self.controls.find('.rs-be-add-image-block').click(function() {
			self.admin.showThickbox('media-upload.php?type=image&post_id=&newrs-a-gallery-enabled=true&newrs-a-gallery-single=true&TB_iframe=true', function(response) {
				var el = self.getEmptyBlock();
				el.find('.rsABlock').html('<img class="rsABImage" src="'+response.large+'" />');
				el.css({
					width: response.large_width,
					height: response.large_height
				})
				self.addBlock( el );
			});
		});


		//$('#block-classes-select').appendTo(self.buttonsHolder);
		self.container.append(self.buttonsHolder);

		
		self.animCheckbox = self.controls.find('.animation-cb input').change(function() {
			self.updateAnimatedOptions();
		});

		// self.buttonsHolder = $('<div class="rs-be-view-buttons">View:</div>');
		// $('<button class="button">Desktop</button>').click(function() {

		// }).appendTo(self.buttonsHolder);
		// $('<button class="button">Tablet</button>').click(function() {

		// }).appendTo(self.buttonsHolder);
		// $('<button class="button">Phone</button>').click(function() {
		// 	self.addBlock();
		// }).appendTo(self.buttonsHolder);
		// self.container.append(self.buttonsHolder);

		self.blocksList = self.controls.find('.rs-be-blocks-list');
		self.editorArea = self.controls.find('.rs-be-editorarea'); 
		self.inputs = self.controls.find('.size-fields');
		self.animInputs = self.controls.find('.transition-fields');

		self.controls.delegate('.rs-help-el', 'mouseover', function(event) {
			var isAtTop = $(this).data('align') === 'top';

			$(this).qtip({
				overwrite: false,
				content: {
					attr: 'data-help'
				},
				position: {
					at: isAtTop ? 'top center' : 'center left', 
					my: isAtTop ? 'bottom center' : 'center right', 
				},
				style: {
					classes: 'ui-tooltip-rounded ui-tooltip-shadow ui-tooltip-tipsy rs-tooltip'
				},
				show: {
					event: event.type,
					ready: true,
					effect: false,
					delay: 400
				},
				hide: {
					effect: false
				}
			});
		});


		


		
		self.show();
		
		$(self.animatedBlocks).delegate('.rsABlockContainer', 'click.rsbe', function() {
			self.setSelectedBlock( $(this).data('b-id'));
			return false;
		});
		$(self.animatedBlocks).click(function() {
			self.setSelectedBlock(null);
		});

		self.blocksList.sortable({
			update:function(event, ui) {
				var itemId = $(ui.item).data('b-id');
				var block = self.animatedBlocks.children().filter(function() {
				    return $(this).data('b-id') == itemId;
				});

				block.detach();
				var befItem = self.animatedBlocks.children().eq(ui.item.index());

				if(befItem.length) {
					befItem.before(block);
				} else {
					self.animatedBlocks.append(block);
				}
			}
		}).delegate('.rs-be-list-block', 'click', function(e) {
			var item = $(this);
			if( $(e.target).hasClass('rs-be-list-remove')) {
				self.removeBlock( $(e.target).parent().data('b-id') );
				return false;
			}

			self.setSelectedBlock(item.data('b-id'));
			
		});




		var dragOptions = {
				snap:'.rs-be-a-block',
				snapTolerance: 5,
				cursor: 'move',
				delay: 100,
				drag:function(e, ui) {		
					self.inputs.find('#rs-a-b-left').val( parseInt(ui.position.left, 10) );
					self.inputs.find('#rs-a-b-top').val( parseInt(ui.position.top, 10) );
				},
				stop: function(e, ui) {
					if(self.currBlock) {
						self.currBlock.css({
							left: parseInt(ui.position.left, 10),
							top: parseInt(ui.position.top, 10)
						});
					}
					
				}
		};	
		var resizeOptions = {
			handles: "all", 
			autoHide: true, 
			containment: 'parent',
			delay: 20,
			resize: function(e, ui) {
				self.inputs.find('#rs-a-b-width').val( ui.size.width );
				self.inputs.find('#rs-a-b-height').val( ui.size.height );
			}
		};
		$(self.animatedBlocks).delegate('.rsABlockContainer', 'mouseover.rsbe', function() {
			if (!$(this).data("rs-dr-init")) {
		        $(this)
		        	.data("rs-dr-init", true)
		        	.draggable(dragOptions)
		        	.resizable(resizeOptions);
		    }
		});


		self.init();

		self.bindPosSizeFields();

		self.blockClassesSelect = $('#block-classes-select');
		self.blockClassesSelect.change(function() {
			var val = $(this).val();
			var values = self.getListOfAllBlockClasses();
			if(val && val !== 'rs_add_user_class' && val !== 'rs_remove_user_class') {
				if(self.currBlock) {
					if(values) {
						self.currBlock.removeClass(values);
					}
					self.currBlock.addClass(val);
				}
			}
			if(!val) {
				if(values)
					self.currBlock.removeClass(values);
			}
		});

		jQuery('#rs-be').children().not('#rs-be-buttons, .rs-be-blocks-list').css('visibility','hidden');
	}


	NewRSBlockEditor.prototype = {
		addBlock: function(el) {
			var self = this;
			self.maxItemId++;

			self.blocksList.children().removeClass('rs-be-list-current');
			
			self.addListEl();

			var block = !el ? self.getEmptyBlock() : el;
			block.data('b-id', self.maxItemId);
			self.animatedBlocks.append(block);

			self.setCurrListText(block);

			self.setSelectedBlock(self.maxItemId);

		},
		addListEl: function(notCurrent) {
			var self = this;
			var listEl = $('<div class="rs-be-list-block"><span></span><div class="rs-be-list-remove"></div></div>');
			//if()
			self.currBlockListItem = listEl;
			listEl.data('b-id', self.maxItemId);
			self.blocksList.append(listEl);
		},
		updateAnimatedOptions: function() {
			var self = this;
			if(self.animCheckbox.is(':checked')) {
				self.controls.addClass('with-animation-options');
				self.currBlock.removeClass('rsSBlock');
				for(var i = 0; i < self.idatalist.length; i++) {
					var opt = self.idatalistAdmin[i];
					var v = self.admin.getOpt( opt, 'block' );

					if(opt === 'delay') {
						v = 'auto';
					}

					self.animInputs.find('#rs-a-b-' + self.idatalist[i]).rsSetVal( v );
				}
			} else {
				self.controls.removeClass('with-animation-options');
				self.currBlock.addClass('rsSBlock');
			}
		},
		setSelectedBlock: function(id) {
			var self = this;

			if(self.currBlockId === id) {
				return;
			}

			if(self.currBlockListItem && self.currBlock) {
				self.currBlock.removeClass('rs-be-selected');
				self.currBlockListItem.removeClass('rs-be-list-current');
			}
			//if(self.editor)
			//		self.editor.setOption('onChange', null);
			if( (!id && id !== 0) ) {
				self.currBlock = null;
				jQuery('#rs-be').children().not('#rs-be-buttons, .rs-be-blocks-list').css('visibility','hidden');
				self.blockClassesSelect.val('');
				self.currBlockId = null;
				return;
			} else {
				jQuery('#rs-be').children().not('#rs-be-buttons, .rs-be-blocks-list').css('visibility','visible');
			}
			self.currBlockId = id;

			
			//self.inputs.find('input').trigger('change.rsbe');
			self.currBlock = self.animatedBlocks.children().filter(function() {
			    return $(this).data('b-id') == id;
			});
			self.currBlockReal = self.currBlock.find('.rsABlock');
			self.currBlockListItem = self.blocksList.children().filter(function() {
			    return $(this).data('b-id') == id;
			});
			
			self.animCheckbox.rsSetVal( !self.currBlock.hasClass('rsSBlock') );
			self.updateAnimatedOptions();

			
			for(var i = 0; i < self.ilist.length; i++) {
				var res = self.currBlock[0].style[self.ilist[i]];
				self.inputs.find('#rs-a-b-' + self.ilist[i]).val( res );
			}

			for(var i = 0; i < self.idatalist.length; i++) {
				self.animInputs.find('#rs-a-b-' + self.idatalist[i]).rsSetVal( self.currBlock.attr('data-' + self.idatalist[i]) );
			}
			
			self.currBlockListItem.addClass('rs-be-list-current');


			
			self.editor.setValue( self.currBlockReal.html() );
			self.editor.focus();
			var theClass = self.currBlock.clone().removeClass('rsABlockContainer rsSBlock rsABlockContainer ui-draggable ui-resizable rs-be-selected ui-resizable-autohide').attr('class').split(/\s+/);
			if(theClass) {
				var l = theClass.length;
				self.blockClassesSelect.val( theClass[l-1] );
			}

			self.currBlock.addClass('rs-be-selected');
		},
		getListOfAllBlockClasses: function(asArray) {
			var self = this,
				values = asArray ? [] : '',
				val;
			self.blockClassesSelect.children().each(function() {
				val = $(this).val();
				if(asArray) {
					values.push( val );
				} else {
					values += val + ' ';
				}
				
			});
			return values;
		},
		getEmptyBlock: function() {
			return $('<div class="rsABlockContainer rsSBlock" style="width: 120px; height: 60px; left: 5px; top: 5px; "><div class="rsABlock">' + newRsBeVars.new_block_text + '</div></div>');
		},
		setCurrListText: function(el) {
			var self = this;
			var text;

			if(typeof el !== 'string') 
				text = el.text();
			else
				text= el;

			var div = document.createElement("div");
			div.innerHTML = text;
			text = div.textContent || div.innerText || "";
			if(text.length > 10) {
				text = text.substring(0, 10) + '…';
			}

			if($.trim(text).length > 0 ) {
				self.currBlockListItem.find('span').text( text );
			} else {
				self.currBlockListItem.find('span').text( newRsBeVars.no_text );
			}
			
		},
		removeBlock: function(id) {
			var self = this;
			var block = self.animatedBlocks.children().filter(function() {
				return $(this).data('b-id') == id;
			}).remove();
			if(block.hasClass('rs-be-list-current')) {
				self.setSelectedBlock(null);
			}
			self.currBlockListItem = self.blocksList.children().filter(function() {
				return $(this).data('b-id') == id;
			}).remove();
		},

		bindPosSizeFields: function() {
			var self = this;
			//var inputs = self.currBlockInputs.find('.rs-be-subitem').find('input');
			var eName = 'change.rsbe';


			for(var q = 0; q < self.idatalist.length; q++) {
				self.animInputs.find('#rs-a-b-' + self.idatalist[q]).bind(eName, function() {
					var id = $(this).data('list-index');
					var val = $(this).rsGetVal();
					//var val = getVal($(this));
					
					if(self.currBlock)
						self.currBlock.attr('data-' + self.idatalist[ id ], val );

				}).data('list-index', q);
			}

			function getVal(input) {
				var val = input.val();

				
				if( !$.trim(val).length ) {
					input.val('auto');
					return 'auto';
				}

				if(val.indexOf('%') > 0) {
					return val;
				} else if(val === 'auto') {
					return val;
				} else if(val.indexOf('px') > 0) {
					val = parseInt(val, 10);
				}

				val = parseInt(val, 10);

				return val;
			}

			for(var i = 0; i < self.ilist.length; i++) {
				self.inputs.find('#rs-a-b-' + self.ilist[i]).bind(eName, function() {
					var val = getVal($(this));

					self.currBlock.css( self.ilist[ $(this).data('list-index') ], val );
				}).data('list-index', i);
			}

		},
	
		init: function() {
			var self = this;
			self.animatedBlocks.empty();
			self.blocksList.empty();
			var currBlocks = $( self.el.find('.rs-anim-block-textarea').val() );

			if( !self.el.find('.rs-blocks-editor').length ) {
				self.el.append(self.container);
			}
			

			currBlocks.find('.rsSBlock').addClass('rsABlock');
			currBlocks.each(function() {
				
				var item = $(this);
				item.removeClass('rsABlock').addClass('rsABlockContainer').contents().wrapAll('<div class="rsABlock"></div>');
				item.data('b-id', self.maxItemId);
				self.addListEl(true);
				self.animatedBlocks.append(item);
				self.setCurrListText(item);
				self.maxItemId++;
			});
			self.slideArea.attr('class', 'rs-be-slidearea ' + $('#skin-select').val() );

		},
		show: function(el) {
			var self = this;

			if(self.hidden) {
				self.el = el;
				self.init();

				self.hidden = false;
			}
			self.isDisplayed = true;

			if(!self.editorArea.find('.newrsCmEditor').length) {
				self.editor = window.newrsGetCodeMirror( self.editorArea.find('textarea'), true, 'default' );
				self.editor = self.editor[0].cmInstance;
				
				self.editor.setSize('100%', '168px');
				self.editor.setOption('onChange', function(editor) {
		        	var val = self.editor.getValue();
		        	if(self.currBlockReal) {
		        		self.setCurrListText( val );
		        		self.currBlockReal.html(val);
		        	}
		        });
			} 

			if(!self.editor) {

			}

			if(self.hidden) {
				self.init();
			}
			
			self.bgImage.attr('src', self.admin.getImage() );
			// self.popup
			self.admin.currTabsDialog.extractor({ 
				height: 'auto',
				width: '800px'
			}).bind( "dialogresize", function( event, ui ) {
				self.updateSize();
			} );

			self.updateSize();

			setTimeout(function() {

				self.admin.currTabsDialog.rsdialog('option', 'position', 'center');
				var dWindow = self.admin.currTabsDialog.closest('.ui-dialog');
				if( parseInt( dWindow.css('top'), 10) < 40 )  {
					dWindow.css('top', '40px');
				}
			}, 20);
		},
		hide: function() {
			var self = this;
			if(self.isDisplayed) {
				self.isDisplayed = false;
				var blocks = self.animatedBlocks.clone();
				var realBlocks = $('<div></div>');
				var children = blocks.children();
				self.hidden = true;
				self.container.detach();
				if(children.length) {
					children.each(function() {
						var item = $(this).removeClass('rsABlockContainer ui-draggable ui-resizable ui-resizable-autohide rs-be-selected');

						if( item.data("rs-dr-init") ) {
							item.resizable('destroy').draggable('destroy');
						}
						

						item.html(item.find('.rsABlock, .rsSBlock').eq(0).contents());
						if(!item.hasClass('rsSBlock')) {
							item.addClass('rsABlock');
						}
						realBlocks.append( item );
					});
				}
				var html = realBlocks.html();
				self.el.find('.rs-anim-block-textarea').html( html ).val(html);
				self.setSelectedBlock(null);
				self.animatedBlocks.empty();
			}
		},
		destroy: function() {
			var self = this;


			//self.animatedBlocks = $('<div class="rs-animated-blocks"><div class="rsABlockContainer ui-draggable ui-resizable ui-resizable-autohide" style="opacity: 1; left: 559.0000305175781px; top: 95.00000762939453px; position: absolute; width: 188px; height: 98px;"><div class="rsABlock"><h3>Title</h3></div><div class="ui-resizable-handle ui-resizable-n" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 1000; display: none;"></div></div><div class="rsABlockContainer ui-draggable ui-resizable rs-be-selected ui-resizable-autohide testClass1" style="opacity: 1; left: 30%; top: auto; right: 30%; bottom: 20px; width: auto;" data-speed="200"><div class="rsABlock"><h3>Block HTML text</h3></div><div class="ui-resizable-handle ui-resizable-n" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 1000; display: none;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 1000; display: none;"></div></div></div>');
			
			var blocks = self.animatedBlocks.clone();
			var realBlocks = $('<div></div>');
			var children = blocks.children();

			if(children.length) {
				children.each(function() {
					var item = $(this).removeClass('rsABlockContainer ui-draggable ui-resizable ui-resizable-autohide rs-be-selected');

					if( item.data("rs-dr-init") ) {
						item.resizable('destroy').draggable('destroy');
					}
					

					item.html(item.find('.rsABlock').contents());
					item.addClass('rsABlock');
					realBlocks.append( item );
				});
			}
			var html = realBlocks.html();
			self.el.find('.rs-anim-block-textarea').html( html ).val( html );

			self.setSelectedBlock(null);
			self.container.remove();
		},
		updateSize: function() {
			var self = this;
			var dim = self.admin.getDimensions();

			self.slideArea.css('width', dim.width);
			setTimeout(function() {
				if( self.admin.getOpt('autoScaleSlider') ) {
					dim.height = self.slideArea.width() * ( self.admin.getOpt('autoScaleSliderHeight') / self.admin.getOpt('autoScaleSliderWidth') );
				}

				self.slideArea.css('height', dim.height);
				self.resizeImage(self.bgImage);
			}, 10);
		},
		resizeImage:function(img) {
			var self = this;
			var imgScaleMode = self.admin.getOpt('imageScaleMode');
			var imgAlignCenter = self.admin.getOpt('imageAlignCenter');
			var imgScalePadding = self.admin.getOpt('imageScalePadding');
			
			var baseImageWidth = self.admin.getImageWidth();
				baseImageHeight = self.admin.getImageHeight();


			if(imgScaleMode === 'none' && !imgAlignCenter) {
				return;
			}
			if(imgScaleMode !== 'fill') {
				bMargin = imgScalePadding;
			} else {
				bMargin = 0;
			}
			//var block = img.parent('.block-inside').css('margin', bMargin);
			var containerWidth = self.slideArea.width() - bMargin * 2,
				containerHeight = self.slideArea.height() - bMargin * 2,
				hRatio,
				vRatio,
				ratio,
				nWidth,
				nHeight,
				cssObj = {};

			if(imgScaleMode === 'fit-if-smaller') {
				if(baseImageWidth > containerWidth || baseImageHeight > containerHeight) {
					imgScaleMode = 'fit';
				}
			}
			if(imgScaleMode === 'fill' || imgScaleMode === 'fit') {		
				hRatio = containerWidth / baseImageWidth;
				vRatio = containerHeight / baseImageHeight;

				if (imgScaleMode  == "fill") {
					ratio = hRatio > vRatio ? hRatio : vRatio;                    			
				} else if (imgScaleMode  == "fit") {
					ratio = hRatio < vRatio ? hRatio : vRatio;             		   	
				} else {
					ratio = 1;
				}

				nWidth = Math.ceil(baseImageWidth * ratio, 10);
				nHeight = Math.ceil(baseImageHeight * ratio, 10);
			} else {								
				nWidth = baseImageWidth;
				nHeight = baseImageHeight;		
			}
			if(imgScaleMode !== 'none') {
				cssObj.width = nWidth;
				cssObj.height = nHeight;
			}
			if (imgAlignCenter) {     
				cssObj.marginLeft = Math.floor((containerWidth - nWidth) / 2) +  bMargin;
				cssObj.marginTop = Math.floor((containerHeight - nHeight) / 2) +  bMargin;
			}
			img.css(cssObj);
		}
	};

	$.fn.newRSBlockEditor = function(admin, el, popup) {    
		return this.each(function(){
			var self = $(this);
			var o = new NewRSBlockEditor(self, el, admin, popup);
			$(this).data('newRSBlockEditor', o);
		});
	};
})(jQuery);