(function ($) {
  
    var theme = $('#spcc_selected_theme').val();
  
    if ($('#editor').length > 0) {
        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/" + theme);
        editor.getSession().setMode("ace/mode/css");

        $('#spcc_style').val(editor.getSession().getValue());
        editor.getSession().on('change', function () {
            $('#spcc_style').val(editor.getSession().getValue());
        });
    }

    if ($('#editor_lg').length > 0) {
        var lgeditor = ace.edit("editor_lg");
        lgeditor.setTheme("ace/theme/" + theme);
        lgeditor.getSession().setMode("ace/mode/css");
        $('#spcc_style_lg').val(lgeditor.getSession().getValue());
        lgeditor.getSession().on('change', function () {
            $('#spcc_style_lg').val(lgeditor.getSession().getValue());
        });
    }

    if ($('#editor_md').length > 0) {
        var mdeditor = ace.edit("editor_md");
        mdeditor.setTheme("ace/theme/" + theme);
        mdeditor.getSession().setMode("ace/mode/css");
        $('#spcc_style_md').val(mdeditor.getSession().getValue());
        mdeditor.getSession().on('change', function () {
            $('#spcc_style_md').val(mdeditor.getSession().getValue());
        });
    }

    if ($('#editor_sm').length > 0) {
        var smeditor = ace.edit("editor_sm");
        smeditor.setTheme("ace/theme/" + theme);
        smeditor.getSession().setMode("ace/mode/css");
        $('#spcc_style_sm').val(smeditor.getSession().getValue());
        smeditor.getSession().on('change', function () {
            $('#spcc_style_sm').val(smeditor.getSession().getValue());
        });
    }

    if ($('#editor_xs').length > 0) {
        var xseditor = ace.edit("editor_xs");
        xseditor.setTheme("ace/theme/" + theme);
        xseditor.getSession().setMode("ace/mode/css");
        $('#spcc_style_xs').val(xseditor.getSession().getValue());
        xseditor.getSession().on('change', function () {
            $('#spcc_style_xs').val(xseditor.getSession().getValue());
        });
    }

    if ($('#editor_less').length > 0) {
        var lesseditor = ace.edit("editor_less");
        lesseditor.setTheme("ace/theme/" + theme);
        lesseditor.getSession().setMode("ace/mode/less");
        $('#spcc_style').val(lesseditor.getSession().getValue());
        lesseditor.getSession().on('change', function () {
            $('#spcc_style').val(lesseditor.getSession().getValue());
        });
    }

    if ($('#editor_sass').length > 0) {
        var sasseditor = ace.edit("editor_sass");
        sasseditor.setTheme("ace/theme/" + theme);
        sasseditor.getSession().setMode("ace/mode/sass");
        $('#spcc_style_sass').val(sasseditor.getSession().getValue());
        sasseditor.getSession().on('change', function () {
            $('#spcc_style_sass').val(sasseditor.getSession().getValue());
        });
    }

    if ($('#sngleeditor').length > 0) {
        var sceditor = ace.edit("sngleeditor");
        sceditor.setTheme("ace/theme/" + theme);
        sceditor.getSession().setMode("ace/mode/css");
        $('#single_custom_css').val(sceditor.getSession().getValue());
        sceditor.getSession().on('change', function () {
            $('#single_custom_css').val(sceditor.getSession().getValue());
        });
    }
    if ($('#sngleeditorjs').length > 0) {
        var sjditor = ace.edit("sngleeditorjs");
        sjditor.setTheme("ace/theme/" + theme);
        sjditor.getSession().setMode("ace/mode/javascript");
        $('#single_custom_js').val(sjditor.getSession().getValue());
        sjditor.getSession().on('change', function () {
            $('#single_custom_js').val(sjditor.getSession().getValue());
        });
    }

    if ($('#editor_js').length > 0) {
        var jseditor = ace.edit("editor_js");
        jseditor.setTheme("ace/theme/" + theme);
        jseditor.getSession().setMode("ace/mode/javascript");
        $('#spcc_style_js').val(jseditor.getSession().getValue());
        jseditor.getSession().on('change', function () {
            $('#spcc_style_js').val(jseditor.getSession().getValue());
        });
    }

    if ($('#editor_js_footer').length > 0) {
        var jseditor = ace.edit("editor_js_footer");
        jseditor.setTheme("ace/theme/" + theme);
        jseditor.getSession().setMode("ace/mode/javascript");
        $('#spcc_style_js_footer').val(jseditor.getSession().getValue());
        jseditor.getSession().on('change', function () {
            $('#spcc_style_js_footer').val(jseditor.getSession().getValue());
        });
    }
}(window.jQuery));


