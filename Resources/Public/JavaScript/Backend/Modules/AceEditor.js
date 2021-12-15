define(['ace/ace', 'jquery', 'TYPO3/CMS/Backend/Notification'], function (ace, jQuery, Notification) {
    this.editor = ace.edit('editor');
    this.editor.setTheme('ace/theme/monokai');
    this.editor.getSession().setMode('ace/mode/yaml');

    const saveButton = document.querySelector(".editor-save")
    saveButton.addEventListener('click', () => {
        const form = document.getElementById('editor-form-save')
        const idElement = document.getElementById('id')
        const idNamespaceElement = document.getElementById('idNamespace')
        let id = undefined
        let idNamespace = undefined

        if(idElement && idNamespaceElement) {
            id = idElement.value
            idNamespace = idNamespaceElement.value
        }

        let content = this.editor.getSession().getValue();
        const contentNamespace = document.getElementById('contentNamespace').value
        let uriSave = form.getAttribute('action')

        if(idNamespace) {
            uriSave = uriSave + "&" + idNamespace + "=" + id + "&" + contentNamespace + "=" + encodeURI(content)
        } else {
            uriSave = uriSave + "&" + contentNamespace + "=" + encodeURI(content)
        }

        fetch(uriSave).then(
            function(response) {
                if (response.status !== 200) {
                    console.log('Looks like there was a problem. Status Code: ' +
                        response.status);
                    return false;
                } else {
                    return response.json();
                }
            }
        ).then(function (data) {
            if (data.status === 'Ok') {
                Notification.success(TYPO3.lang.saved)

            } else {
                Notification.error(TYPO3.lang.not_saved)
            }
        })
        return false
    })
})
