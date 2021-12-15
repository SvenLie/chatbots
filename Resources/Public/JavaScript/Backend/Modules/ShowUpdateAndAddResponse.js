define([], function() {
    let responseNameSelect = document.getElementById("responseName")

    responseNameSelect.addEventListener('change', () => {
        let responseNameOtherField = document.getElementById("responseNameOther")
        if(responseNameSelect.value !== '-1') {
            responseNameOtherField.closest('div').classList.add('hidden')
            responseNameOtherField.disabled = true
        } else {
            responseNameOtherField.disabled = false
            responseNameOtherField.closest('div').classList.remove('hidden')
        }

    })
})
