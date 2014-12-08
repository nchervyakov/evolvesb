$(function() {
    $('#side-menu').metisMenu();
});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse')
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse')
        }

        height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    $('.file-input').bootstrapFileInput();

    $(document).on('click', '.js-delete-item', function (ev) {
        ev.preventDefault();
        var $link = $(ev.target);
        if (confirm("Are you sure you want to delete this itrm?")) {
            $.ajax({
                url: $link.attr('href'),
                type: 'POST',
                dataType: 'json'
            }).success(function (res) {
                if (res.location) {
                    location.href = res.location;
                }
            }).error(function () {
                alert('Error while deleting the item.')
            });
        }
    });


    $('.js-add-enquiry-message-form').each(function () {
        var form = $(this),
            errorBox = $('.js-add-enquiry-message-errors'),
            list = $('#enquiry_messages');

        form.on('submit', function (ev) {
            ev.preventDefault();

            errorBox.html('');
            var data = form.serialize();
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                dataType: 'json',
                data: data
            }).success(function (res) {
                if (res.success && res.enquiryMessage) {
                    list.append(res.html);
                } else {
                    errorBox.html(res.message);
                }
            });
        })
    });


    $(function () {
        var editors = $('textarea.js-editor');
        if (editors.length || $('body').hasClass('js-has-editor')) {
            var timeout;

            $('head').append(
                "<script src=\"/js/ckeditor/ckeditor.js\"></script>"
                + "<script id=\"js_ckeditor_jq\" src=\"/js/ckeditor/adapters/jquery.js\"></script>"
            );

            var onScriptLoad = function () {
                editors.ckeditor();
            };

            var checkScriptLoaded = function () {
                if (window.CKEDITOR) {
                    clearTimeout(timeout);
                    onScriptLoad();
                } else {
                    timeout = setTimeout(checkScriptLoaded, 50);
                }
            };

            checkScriptLoaded();
        }

        (function () {
            var counter = 0;
            $('.js-alias-field').each(function () {
                var el = $(this),
                    id = 'alias_suggester_' + counter++,
                    suggester, sources;
                el.after('<div class="alert alert-info alias-suggester js-alias-suggester alert-dismissable" id="' + id + '">'
                        + '<a href="#" class="close js-close">&times;</a>'
                        + 'Suggested alias: <span class="js-suggested-alias suggested-alias"></span> '
                        + '<a href="#" class="js-apply-alias">Apply</a></div>');
                suggester = $('#'+id);
                suggester.css({'width': el.css('width')});
                sources = el.closest('form').find('.js-suggest-source');

                var timeout;
                var delayedWaiter = function (alias) {
                    $.ajax({
                        url: '/admin/pages/suggest-alias',
                        type: 'GET',
                        data: {alias: alias},
                        dataType: 'json',
                        complete: function () {
                            timeout = null;
                        }
                    }).success(function (res) {
                        if (el.val() == res.alias) {
                            suggester.hide();
                        } else {
                            suggester.show();
                            suggester.find('.js-suggested-alias').html(res.alias);
                        }
                    });
                };
                sources.on('keypress', function (ev) {
                    if (timeout) {
                        clearTimeout(timeout);
                    }
                    timeout = setTimeout(function () {
                        delayedWaiter($(ev.target).val());
                    }, 1000);
                });

                suggester.on('click', '.js-close', function (ev) {
                    ev.preventDefault();
                    suggester.hide();
                });

                suggester.on('click', '.js-apply-alias', function (ev) {
                    ev.preventDefault();
                    suggester.hide();
                    el.val(suggester.find('.js-suggested-alias').text());
                });
            });
        })();
    });
});

function CkEditorURLTransfer(url)
{
    window.parent.CKEDITOR.tools.callFunction(1, url, '');
    $('#cke_111_textInput').val(url);

}