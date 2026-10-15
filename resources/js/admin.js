import 'bootstrap';
import {
    ClassicEditor,
    Essentials,
    Paragraph,
    Heading,
    Style,
    Bold,
    Italic,
    Strikethrough,
    RemoveFormat,
    Link,
    List,
    Indent,
    IndentBlock,
    BlockQuote,
    HorizontalLine,
    Table,
    TableToolbar,
    SpecialCharacters,
    SpecialCharactersEssentials,
    Fullscreen,
    SourceEditing,
    GeneralHtmlSupport
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';

const editorElement = document.querySelector('#page-html-editor');

if (editorElement) {
    const startInSourceMode = editorElement.dataset.startMode === 'source';

    const toolbarItems = [
        'undo',
        'redo',
        '|',
        'bold',
        'italic',
        'strikethrough',
        'removeFormat',
        '|',
        'bulletedList',
        'numberedList',
        'outdent',
        'indent',
        '|',
        'blockQuote',
        'link',
        'insertTable',
        'horizontalLine',
        '|',
        'style',
        'heading',
        '|',
        'specialCharacters',
        'fullscreen'
    ];

    if (!startInSourceMode) {
        toolbarItems.push('sourceEditing');
    }

    ClassicEditor.create(editorElement, {
        licenseKey: 'GPL',

        plugins: [
            Essentials,
            Paragraph,
            Heading,
            Style,
            Bold,
            Italic,
            Strikethrough,
            RemoveFormat,
            Link,
            List,
            Indent,
            IndentBlock,
            BlockQuote,
            HorizontalLine,
            Table,
            TableToolbar,
            SpecialCharacters,
            SpecialCharactersEssentials,
            Fullscreen,
            SourceEditing,
            GeneralHtmlSupport
        ],

        toolbar: toolbarItems,

        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
        },

        htmlSupport: {
            allow: [
                {
                    name: /.*/,
                    attributes: true,
                    classes: true,
                    styles: true
                }
            ],

            disallow: [
                { name: 'script' },
                { name: 'style' },
                { attributes: [{ key: /^on.*$/i, value: true }] }
            ]
        }
    })
    .then(editor => {
        if (!startInSourceMode) {
            return;
        }

        const enableSourceMode = () => {
            const sourceEditing = editor.plugins.get('SourceEditing');

            if (sourceEditing && !sourceEditing.isSourceEditingMode) {
                sourceEditing.isSourceEditingMode = true;
            }
        };

        const htmlModeRadio = document.querySelector('#mode_custom');

        if (!htmlModeRadio || htmlModeRadio.checked) {
            enableSourceMode();
        } else {
            htmlModeRadio.addEventListener('change', enableSourceMode, { once: true });
        }
    })
    .catch(error => {
        console.error(error);
    });
}

const designTabButton = document.querySelector('#page-tab-design-btn');

if (designTabButton) {
    designTabButton.addEventListener('shown.bs.tab', () => {
        window.dispatchEvent(new Event('resize'));
    });
}

document.querySelectorAll('input[name="mode"]').forEach(radio => {
    radio.addEventListener('change', () => {
        window.dispatchEvent(new Event('resize'));
    });
});

let fmTargetInput = null;
let fmTargetPreview = null;

window.fmSelect = function (url) {
    if (fmTargetInput) {
        fmTargetInput.value = url;
    }

    if (fmTargetPreview) {
        fmTargetPreview.src = url;
    }
};

document.querySelectorAll('.js-pick-image').forEach(function (button) {
    button.addEventListener('click', function () {
        fmTargetInput = document.getElementById(button.dataset.input);
        fmTargetPreview = document.getElementById(button.dataset.preview);
        window.open(button.dataset.fmUrl, 'filemanager', 'width=720,height=560,resizable=yes,scrollbars=yes');
    });
});

document.querySelectorAll('.js-clear-image').forEach(function (button) {
    button.addEventListener('click', function () {
        const input = document.getElementById(button.dataset.input);
        const preview = document.getElementById(button.dataset.preview);

        if (input) {
            input.value = '';
        }

        if (preview) {
            preview.src = preview.dataset.placeholder || '';
        }
    });
});