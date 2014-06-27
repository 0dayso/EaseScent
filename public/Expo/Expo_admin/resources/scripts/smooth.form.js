/* this function styles inputs with the type file. It basically replaces browse or choose with a custom button */
(function ($) {
    $.fn.file = function (options) {
        var settings = {
            width: 250
        };

        if (options) {
            $.extend(settings, options);
        }

        this.each(function () {
            var self = this;

            var wrapper = $("<a>").attr("class", "ui-input-file");

            var filename = $('<input class="file">').addClass($(self).attr("class")).css({
                "display": "inline",
                "width": settings.width + "px"
            });

            $(self).before(filename);
            $(self).wrap(wrapper);

            $(self).css({
                "position": "relative",
                "height": settings.image_height + "px",
                "width": settings.width + "px",
                "display": "inline",
                "cursor": "pointer",
                "opacity": "0.0"
            });

            if ($.browser.mozilla) {
                if (/Win/.test(navigator.platform)) {
                    $(self).css("margin-left", "-142px");
                } else {
                    $(self).css("margin-left", "-168px");
                };
            } else {
                $(self).css("margin-left", settings.image_width - settings.width + "px");
            };

            $(self).bind("change", function () {
                filename.val($(self).val());
            });
        });

        return this;
    };
})(jQuery);

$(document).ready(function () {
    $("input.focus, textarea.focus").focus(function () {
        if (this.value == this.defaultValue) {
            this.value = "";
        }
        else {
            this.select();
        }
    });

    $("input.focus, textarea.focus").blur(function () {
        if ($.trim(this.value) == "") {
            this.value = (this.defaultValue ? this.defaultValue : "");
        }
    });

    /* date picker */




    $(".date").datepicker({
        dateFormat: "yy-mm-dd",
        showOn: 'both',
        buttonImage: PUBLIC+'/Expo/Expo_admin/resources/images/ui/calendar.png',
        dayNames: [ "星期天", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六" ],
        buttonImageOnly: false
    });





    /* select styling */
    $("select").selectmenu({
        style: 'dropdown',
        width: 200,
        menuWidth: 200,
        icons: [
		    { find: '.locked', icon: 'ui-icon-locked' },
		    { find: '.unlocked', icon: 'ui-icon-unlocked' },
		    { find: '.delete', icon: 'ui-icon-trash' },
            { find: '.goods_up', icon: 'ui-icon-carat-1-n' },
            { find: '.goods_down', icon: 'ui-icon-carat-1-s' },
	    ]
    });

    /* file input styling */
    $("input[type=file]").file({
        image_height: 28,
        image_width: 28,
        width: 250
    });

    /* tinymce (text editor) */

    $("textarea.editor").tinymce({
        script_url: "resources/scripts/tiny_mce/tiny_mce.js",
        mode: "textareas",
        theme: "advanced",
        theme_advanced_buttons1: "newdocument,separator,bold,italic,underline,strikethrough,separator,justifyleft, justifycenter,justifyright,justifyfull,separator,cut,copy,paste,pastetext,pasteword,separator,help",
        theme_advanced_buttons2: "bullist,numlist,separator,outdent,indent,blockquote,separator,undo,redo,separator,link,unlink,anchor,image,cleanup,help,code,separator,forecolor,backcolor",
        theme_advanced_buttons3: "",
        theme_advanced_buttons4: "",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",

        /*textarea默认值的设置*/
        setup : function(ed) {
                var is_default = false;
                ed.onInit.add(function(ed) {
                    ed.focus();
                    // set the focus
                    var cont = ed.getContent();
                    // get the current content
                    slen = cont.length;
                    cont = cont.substring(3,slen-4);
                    // cut off <p> and </p> to comply with XHTML strict
                    // these can't be part of the default_value
                    is_default = (cont == default_value);
                    // compare those strings
                    if (!is_default)
                        return;
                    // nothing to do
                    ed.selection.select(ed.dom.select('p')[0]);
                    // select the first (and in this case only) paragraph
                });
                ed.onMouseDown.add(function(ed,e) {
                    if (!is_default)
                        return;
                    // nothing to do
                    ed.selection.setContent('');
                    // replace the default content with nothing
                });

                // The onload-event in IE fires before TinyMCE has created the Editors,
                // so it is no good solution here.
        }

    });

    /* button styling */
    $("input:submit, input:reset, button").button();
});

