(function($) {
    wp.customize.bind('ready', function() {
        wp.customize.section.each(function(section) {
            if (!$('#sub-accordion-section-' + section.id + ' .customize-section-description').length) {
                $('#sub-accordion-section-' + section.id + ' .customize-section-description-container').append($('<div class="description customize-section-description"></div>'));
            }
            sectionOK = false;
            _.each(wp.customize.section(section.id).controls(), function (control) {
                if (typeof wp.customize(control.id) !== 'undefined') {
                    sectionOK = true;
                }
            });
            if (sectionOK && $('#sub-accordion-section-' + section.id + ' .customize-section-description').length) {
                var $button;
                if (section.id.startsWith('sidebar-widgets-')) {
                    $button = $('<input type="button" id="reset-' + section.id + '" class="button-secondary button" value="' + resetCustomizer.resetPrefix + $('#accordion-section-' + section.id + '>h3').clone().children().remove().end().text() + '">');
                } else {
                    $button = $('<input type="button" id="reset-' + section.id + '" class="button-secondary button" value="' + resetCustomizer.resetPrefix + $('#accordion-section-' + section.id + '>h3').clone().children().remove().end().text() + resetCustomizer.resetSuffix + '">');
                }
                $button.on('click', function () {
                    _.each(wp.customize.section(section.id).controls(), function (control) {
                        if (typeof wp.customize(control.id) !== 'undefined' && section.id.startsWith('sidebar-widgets-')) {
                            wp.customize(control.id).set([]);
                        } else if (typeof wp.customize(control.id) !== 'undefined') {
                            wp.customize(control.id).set('');
                        }
                    });
                    wp.customize.previewer.refresh();
                });
                if ($('#sub-accordion-section-' + section.id + ' .customize-section-description').text().length && !$('#sub-accordion-section-' + section.id + ' .customize-section-description p').length) {
                    $('#sub-accordion-section-' + section.id + ' .customize-section-description').append($('<br /><br />'));
                }
                $('#sub-accordion-section-' + section.id + ' .customize-section-description').append($button);
            }
        });
    });
})(jQuery);
