define([], function() {
    let intentSelect = document.getElementById("trainingDataIntent")

    intentSelect.addEventListener('change', () => {
        let intentOtherField = document.getElementById("trainingDataIntentOther")
        if(intentSelect.value !== '-1') {
            intentOtherField.closest('div').classList.add('hidden')
            intentOtherField.disabled = true
        } else {
            intentOtherField.disabled = false
            intentOtherField.closest('div').classList.remove('hidden')
        }

    })
})
