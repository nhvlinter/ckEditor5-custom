//import ClassicEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
import InlineEditorBase from '@ckeditor/ckeditor5-editor-inline/src/inlineeditor';
//import BalloonEditorBase from '@ckeditor/ckeditor5-editor-balloon/src/ballooneditor';

// import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
// import UploadAdapter from '@ckeditor/ckeditor5-adapter-ckfinder/src/uploadadapter';
// import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat';
// import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
// import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
// import Underline from '@ckeditor/ckeditor5-basic-styles/src/underline';
// import Strikethrough from '@ckeditor/ckeditor5-basic-styles/src/strikethrough';
// import Subscript from '@ckeditor/ckeditor5-basic-styles/src/subscript';
// import Superscript from '@ckeditor/ckeditor5-basic-styles/src/superscript';
// import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';
// import CKFinder from '@ckeditor/ckeditor5-ckfinder/src/ckfinder';
// import Heading from '@ckeditor/ckeditor5-heading/src/heading';
// import Image from '@ckeditor/ckeditor5-image/src/image';
// import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption';
// import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle';
// import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar';
// import ImageUpload from '@ckeditor/ckeditor5-image/src/imageupload';
// import ImageResize from '@ckeditor/ckeditor5-image/src/imageresize';
// import Indent from '@ckeditor/ckeditor5-indent/src/indent';
// import Link from '@ckeditor/ckeditor5-link/src/link';
// import List from '@ckeditor/ckeditor5-list/src/list';
// import MediaEmbed from '@ckeditor/ckeditor5-media-embed/src/mediaembed';
// import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
// import PasteFromOffice from '@ckeditor/ckeditor5-paste-from-office/src/pastefromoffice';
// import Table from '@ckeditor/ckeditor5-table/src/table';
// import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';
// import TextTransformation from '@ckeditor/ckeditor5-typing/src/texttransformation';
// import FontFamily from '@ckeditor/ckeditor5-font/src/fontfamily';
// import FontSize from '@ckeditor/ckeditor5-font/src/fontsize';
// import FontColor from '@ckeditor/ckeditor5-font/src/fontcolor';
// import FontBackgroundColor from '@ckeditor/ckeditor5-font/src/fontbackgroundcolor';

import DIVConversionPlugin from './DIVConversionPlugin';
import LabelConversionPlugin from './LabelConversionPlugin';
import SpanConversionPlugin from './SpanConversionPlugin';
import AnchorConversionPlugin from './AnchorConversionPlugin';

export default class CKEditorCustom extends InlineEditorBase { }
CKEditorCustom.builtinPlugins = [
    // Essentials,
    // UploadAdapter,
    // Autoformat,
    // Bold,
    // Italic,
    // Underline,
    // Strikethrough,
    // Subscript,
    // Superscript,
    // BlockQuote,
    // CKFinder,
    // Heading,
    // Image,
    // ImageCaption,
    // ImageStyle,
    // ImageToolbar,
    // ImageUpload,
    // ImageResize,
    // Indent,
    // Link,
    // List,
    // MediaEmbed,
    // Paragraph,
    // PasteFromOffice,
    // Table,
    // TableToolbar,
    // TextTransformation,
    // FontFamily,
    // FontColor,
    // FontSize,
    // FontBackgroundColor,
    DIVConversionPlugin,
    LabelConversionPlugin,
    SpanConversionPlugin,
    AnchorConversionPlugin
];

// Editor configuration.
CKEditorCustom.defaultConfig = {
    //placeholder: 'Nhập nội dung ở đây!',
    toolbar: {
        //https://stackoverflow.com/questions/52622291/ckeditor-5-toolbar-fixed-position
        viewportTopOffset: 50,
        items: [
            'heading',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'subscript',
            'superscript',
            'link',
            'bulletedList',
            'numberedList',
            '|',
            'indent',
            'outdent',
            '|',
            'imageUpload',
            'blockQuote',
            'insertTable',
            'mediaEmbed',
            'undo',
            'redo',
            '|',
            'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor'
        ]
    },
    image: {
        toolbar: [
            'imageStyle:full',
            'imageStyle:side',
            '|',
            'imageTextAlternative'
        ]
    },
    table: {
        contentToolbar: [
            'tableColumn',
            'tableRow',
            'mergeTableCells'
        ]
    },
    // This value must be kept in sync with the language defined in webpack.config.js.
    language: 'en',
};