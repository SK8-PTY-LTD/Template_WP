(function () {
    tinymce.create('tinymce.plugins.nextend2smartslider3', {
        init: function (ed, url) {
            ed.addButton('nextend2smartslider3', {
                title: 'Smart Slider 3',
                image: url + '/icon.png',
                onclick: function () {
                    NextendSmartSliderWPTinyMCEModal(ed);
                }
            });
        },
        createControl: function (n, cm) {
            return null;
        },
        getInfo: function () {
            return {
                longname: "Smart Slider 3",
                author: 'Nextendweb',
                authorurl: 'http://www.nextendweb.com',
                infourl: 'http://smartslider3.com',
                version: "3.0"
            };
        }
    });
    tinymce.PluginManager.add('nextend2smartslider3', tinymce.plugins.nextend2smartslider3);
})();