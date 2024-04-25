/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function (config) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';
    //config.removeButtons = 'Underline,JustifyCenter';
    config.removeButtons = 'Save,Checkbox,Radio,TextField,Textarea,HiddenField,Button,ImageButton,Print,Flash,Subscript,Superscript,CopyFormatting,RemoveFormat,Anchor,Unlink,Table,Smiley,Iframe,Templates,Image,NewPage,Preview,Strike,NumberedList,HorizontalRule,SpecialChar,Source,Styles,Font,FontSize';
    config.allowedContent = true;
    config.extraAllowedContent = '*(*);*{*}';
    config.removePlugins = 'pagebreak';
    config.forcePasteAsPlainText = true;
    config.enterMode = CKEDITOR.ENTER_BR; //put it bc KS said when enter then take as br instead of p tag
    config.shiftEnterMode = CKEDITOR.ENTER_P;
    config.toolbarGroups = [
        //{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
        //{ name: 'editing',     groups: [ 'find', 'selection' ] },
        //{ name: 'links' },
        {name: 'insert'},
        //{ name: 'forms' },
        //{ name: 'tools' },
        {name: 'document', groups: ['mode', 'document', 'doctools']},
        //{ name: 'others' },
        {name: 'links', groups: ['links']},
        {name: 'basicstyles', groups: ['basicstyles']},
        {name: 'paragraph', groups: ['list', 'align']}, //'indent'
        {name: 'styles', groups: ['Format']},
        //{ name: 'styles' },
        //{ name: 'colors' },
        //{ name: 'about' }
    ];

};


$('.ckeditor').each(function (e) {
    CKEDITOR.replace(this.id, {
        height: 300,

        // Configure your file manager integration. This example uses CKFinder 3 for PHP.
        filebrowserBrowseUrl: '../assets/plugins/ckfinder/ckfinder.html',
        filebrowserImageBrowseUrl: '../assets/plugins/ckfinder/ckfinder.html?type=Images',
        filebrowserUploadUrl: '../assets/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserImageUploadUrl: '../assets/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'
    });
});

$(document).on({
    'show.bs.modal': function () {
        $(this).removeAttr('tabindex');
    }
}, '.modal');