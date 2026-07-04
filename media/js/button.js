import {JoomlaEditorButton} from 'editor-api';
import JoomlaDialog from 'joomla.dialog';

const FIELD_ID = 'mfm-target';

let activeEditor = null;

function getOrCreateHiddenField() {
    let input = document.getElementById(FIELD_ID);

    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.id = FIELD_ID;

        document.body.appendChild(input);
    }

    return input;
}

function pickFile(kind) {

    return new Promise((resolve) => {

        (async () => {
            const hiddenField = getOrCreateHiddenField();
            hiddenField.value = '';

            const settings = Joomla.getOptions('plg_editors_xtd_jufilemanager');
            const ajaxUrl = `${settings.ajaxUrl}&kind=${encodeURIComponent(kind)}&${settings.token}=1`;

            let payload;

            try {
                const response = await fetch(
                    ajaxUrl,
                    {credentials: 'same-origin'}
                );
                const data = await response.json();

                payload = data && data.data !== undefined ? data.data : data;
            } catch (e) {
                Joomla.renderMessages({
                    error: ['Could not reach the file manager.']
                });

                resolve(null);

                return;
            }

            if (!payload || payload.error) {
                Joomla.renderMessages({
                    error: [payload && payload.error ? payload.error : 'Access denied.']
                });

                resolve(null);

                return;
            }

            const dialog = new JoomlaDialog({
                popupType: 'iframe',
                label: Joomla.Text._('PLG_IMAGE_BUTTON_INSERT'),
                location: 'header',
                textHeader: Joomla.Text._(kind === 'file' ? 'PLG_EDITORS_XTD_JUFILEMANAGER_BUTTON_FILE' : 'PLG_EDITORS_XTD_JUFILEMANAGER_BUTTON_IMAGE'),
                src: payload.link,
                width: '95%',
                height: '90vh',
            });

            let settled = false;
            const finish = (url) => {
                if (settled) {
                    return;
                }

                settled = true;

                clearInterval(pollId);
                resolve(url || null);

                dialog.close();
            };

            const pollId = setInterval(() => {
                if (hiddenField.value) {
                    finish(hiddenField.value);
                }
            }, 300);

            window.responsive_filemanager_callback = function (fieldId) {
                const input = document.getElementById(fieldId || FIELD_ID);

                finish(input ? input.value : '');
            };

            dialog.addEventListener('joomla-dialog:load', () => {
                const iframe = dialog.getBodyContent();
                const win = iframe && iframe.contentWindow;

                if (win && !win.opener) {
                    try {
                        win.opener = win.parent;
                    } catch (e) {
                    }
                }
            });

            dialog.addEventListener('joomla-dialog:close', () => {
                clearInterval(pollId);
                Joomla.Modal.setCurrent(null);

                dialog.destroy();

                resolve(settled ? undefined : null);
            });

            Joomla.Modal.setCurrent(dialog);

            dialog.show();
        })();
    });
}

JoomlaEditorButton.registerAction(
    'jufilemanager-open',
    async (editor, options) => {
        activeEditor = editor;

        const kind = (options && options.kind) || 'image';
        const url = await pickFile(kind);

        if (url) {
            const isImage = /\.(png|jpe?g|gif|webp|svg)$/i.test(url);
            const html = isImage ? `<img src="${url}" alt="">` : `<a href="${url}">${url}</a>`;

            activeEditor.replaceSelection(html);
        }
    }
);

function attachBrowseButton(editor) {
    editor.on('OpenWindow', (event) => {
        const dialogApi = event.dialog;

        requestAnimationFrame(() => {
            const dialogRoot = document.querySelector('.tox-dialog:last-of-type');

            if (!dialogRoot) {
                return;
            }

            const urlInput = dialogRoot.querySelector('input[name="src"], input[name="href"]');

            if (!urlInput) {
                return;
            }

            const fieldWrapper = urlInput.closest('.tox-form__group') || urlInput.parentElement;

            if (!fieldWrapper || fieldWrapper.querySelector('.jufilemanager-browse-btn')) {
                return;
            }

            const kind = urlInput.name === 'href' ? 'file' : 'image';

            const browseBtn = document.createElement('button');
            browseBtn.type = 'button';
            browseBtn.className = 'jufilemanager-browse-btn tox-button tox-button--secondary';
            browseBtn.style.marginTop = '4px';
            browseBtn.textContent = Joomla.Text._(
                kind === 'file' ? 'PLG_EDITORS_XTD_JUFILEMANAGER_BUTTON_FILE' : 'PLG_EDITORS_XTD_JUFILEMANAGER_BUTTON_IMAGE'
            );

            browseBtn.addEventListener(
                'click',
                async (e) => {
                    e.preventDefault();
                    activeEditor = null;

                    const url = await pickFile(kind);

                    if (url) {
                        dialogApi.setData({[urlInput.name]: url});
                    }
                }
            );

            fieldWrapper.appendChild(browseBtn);
        });
    });
}

if (window.tinymce) {
    window.tinymce.on(
        'AddEditor',
        (e) => attachBrowseButton(e.editor)
    );

    (window.tinymce.editors || []).forEach(attachBrowseButton);
}