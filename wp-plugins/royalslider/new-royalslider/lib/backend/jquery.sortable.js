
/**
 * @preserve Extractor 1.1
 * Copyright 2011 Kevin Wetzels - Licensed under the MIT License.
 */
(function($) {

    var extractorCount = 0;

    $.fn.extractor = function(opts) {
        var attrKeys = opts.attributesToSave || ['class', 'style'];
        this.each(function() {
            var e = $(this);
            // Since loading the dialog will override the class and style
            // attributes of the content, we want to keep track of their
            // original values.
            var attrs = {};
            for (var i = 0, len = attrKeys.length; i < len; ++i) {
                var k = attrKeys[i];
                var v = e.attr(k);
                attrs[k] = (v ? v : '');
            }
            // Get or generate the id of the content loaded into the modal.
            var id = e.attr('id');
            if (!$.trim(id)) {
                id = 'extractor-' + (++extractorCount);
                e.attr('id', id);
            }
            // Generate the placeholder so we know where to put the content
            // when the dialog is closed.
            var placeholderId = 'extractor-placeholder-' + id;
            $('<div id="' + placeholderId + '"></div>').insertBefore(e).hide();
            // Get a hold of the options we have to alter
            var originalClose = (opts.close ? opts.close : $.noop);
            var c = opts.dialogClass;
            // Override those options
            var options = $.extend(true, opts, {
                close: function(evt, ui) {
                    // Perform the regular close
                    originalClose(evt, ui);
                    // And place the content back where it belongs
                    var o = $('#' + id);
                    o.rsdialog('destroy');
                    o.attr(attrs);
                    o.removeAttr('id');
                    o.removeAttr('style');
                    $('#' + placeholderId).replaceWith(o);
                    // IE needs this
                    //$('#' + id).show();
                },
                dialogClass: (c ? c : '') + ' ui-extractor'
            });
            // Ready to rock.
            e.rsdialog(options);
        });
        return this;
    };

})(jQuery);



/*
 * HTML5 Sortable jQuery Plugin
 * http://farhadi.ir/projects/html5sortable
 * 
 * Copyright 2012, Ali Farhadi
 * Released under the MIT license.
 */
(function($) {
var dragging, placeholders = $();
$.fn.html5Sortable = function(options) {
	var method = String(options);
	options = $.extend({
		connectWith: false
	}, options);
	return this.each(function() {
		if (/^enable|disable|destroy$/.test(method)) {
			var items = $(this).children($(this).data('items')).attr('draggable', method == 'enable');
			if (method == 'destroy') {
				items.add(this).removeData('connectWith items')
					.off('dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s');
			}
			return;
		}
		var isHandle, index, items = $(this).children(options.items);
		var placeholder = $('<' + (/^ul|ol$/i.test(this.tagName) ? 'li' : 'div') + ' class="sortable-placeholder">');
		items.find(options.handle).mousedown(function() {
			isHandle = true;
		}).mouseup(function() {
			isHandle = false;
		});
		$(this).data('items', options.items)
		placeholders = placeholders.add(placeholder);
		if (options.connectWith) {
			$(options.connectWith).add(this).data('connectWith', options.connectWith);
		}
		items.attr('draggable', 'true').on('dragstart.h5s', function(e) {
			if (options.handle && !isHandle) {
				return false;
			}
			isHandle = false;
			//var dt = e.originalEvent.dataTransfer;
			//dt.effectAllowed = 'move';
			//dt.setData('Text', 'dummy');
			index = (dragging = $(this)).addClass('sortable-dragging').index();
		}).on('dragend.h5s', function() {
			if (!dragging) {
				return;
			}
			dragging.removeClass('sortable-dragging').show();
			placeholders.detach();
			if (index != dragging.index()) {
				dragging.parent().trigger('sortupdate', {item: dragging});
			}
			dragging = null;
		}).not('a[href], img').on('selectstart.h5s', function() {
			//this.dragDrop && this.dragDrop();
			return false;
		}).end().add([this, placeholder]).on('dragover.h5s dragenter.h5s drop.h5s', function(e) {
			if (!items.is(dragging) && options.connectWith !== $(dragging).parent().data('connectWith')) {
				return true;
			}
			if (e.type == 'drop') {
				e.stopPropagation();
				placeholders.filter(':visible').after(dragging);
				dragging.trigger('dragend.h5s');
				return false;
			}
			e.preventDefault();
			//e.originalEvent.dataTransfer.dropEffect = 'move';
			if (items.is(this)) {
				if (options.forcePlaceholderSize) {
					placeholder.height(dragging.outerHeight());
				}
				dragging.hide();
				$(this)[placeholder.index() < $(this).index() ? 'after' : 'before'](placeholder);
				placeholders.not(placeholder).detach();
			} else if (!placeholders.is(this) && !$(this).children(options.items).length) {
				placeholders.detach();
				$(this).append(placeholder);
			}
			return false;
		});
		
	});
};
})(jQuery);