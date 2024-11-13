import {
	ClassicEditor,
	AccessibilityHelp,
	AutoLink,
	Autosave,
	BalloonToolbar,
	Bold,
	Essentials,
	Italic,
	Link,
	Paragraph,
	SelectAll,
	Undo
} from 'ckeditor5';

import es from 'ckeditor5/translations/es.js';
import ca from 'ckeditor5/translations/ca.js';
import en from 'ckeditor5/translations/en.js';

const editorConfig = {
	toolbar: {
		items: ['undo', 'redo', '|', 'bold', 'italic', '|', 'link'],
		shouldNotGroupWhenFull: false
	},
	plugins: [AccessibilityHelp, AutoLink, Autosave, BalloonToolbar, Bold, Essentials, Italic, Link, Paragraph, SelectAll, Undo],
	balloonToolbar: ['bold', 'italic', '|', 'link'],
	language: 'en',
	link: {
		addTargetToExternalLinks: true,
		defaultProtocol: 'https://',
		decorators: {
			toggleDownloadable: {
				mode: 'manual',
				label: 'Downloadable',
				attributes: {
					download: 'file'
				}
			}
		}
	},
	menuBar: {
		isVisible: true
	},
	placeholder: 'Type or paste your content here!',
	translations: [en, es, ca]
};

const editors = document.querySelectorAll('.editor')
editors.forEach(editor => {
    ClassicEditor.create(editor, {...editorConfig, language: editor.dataset.language});
});
