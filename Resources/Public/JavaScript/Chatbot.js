window.onload = function () {
    let chatToken = ''
    let startChatButton = document.querySelector("#startChatButton")
    let chat = document.querySelector("#chatbotRasaChat")
    let chatHeader = document.querySelector("#chatbotChatHeader")
    let messagesDiv = document.querySelector("#messages")
    let inputDiv = document.querySelector("#chatbotInputDiv")
    let closeChatButton = document.querySelector("#closeChatButton")
    let sendChatButton = document.querySelector("#sendChatButton")
    let inputField = document.querySelector("#chatbotInputField")
    let initialOpening= true

    if (startChatButton) {
        startChatButton.addEventListener('click', () => {
            const uriStartConversation = '/ajax/chatbot/start-conversation'
            fetch(uriStartConversation, {
                method: 'POST'
            }).then(
                (response) => {
                    if (response.status === 200) {
                        return response.json()
                    }
                }
            ).then(
                (data) => {
                    chatToken = data.sender_token

                    if (chat) {
                        startChatButton.classList.add('hidden')
                        chat.classList.remove('hidden')

                        if (inputField) {
                            inputField.addEventListener('keyup', (event) => {
                                // enter key
                                if (event.keyCode === 13) {
                                    event.preventDefault()
                                    sendChatButton.click()
                                }
                            })
                        }

                        if (chatHeader && messagesDiv && inputDiv && initialOpening) {
                            const heightOfChatHeader = chatHeader.offsetHeight
                            const initialHeightOfMessagesBox = messagesDiv.offsetHeight
                            const heightOfInputDiv = inputDiv.offsetHeight
                            const bottomOfInputDiv = 6
                            const topOfChatHeader = chatHeader.offsetTop

                            if (heightOfChatHeader && topOfChatHeader && heightOfInputDiv && bottomOfInputDiv && initialHeightOfMessagesBox) {
                                messagesDiv.style.marginTop = heightOfChatHeader + topOfChatHeader + "px"
                                messagesDiv.style.marginBottom = heightOfInputDiv + bottomOfInputDiv + "px"
                                messagesDiv.style.height = initialHeightOfMessagesBox - heightOfChatHeader - topOfChatHeader - heightOfInputDiv - bottomOfInputDiv + "px"
                                initialOpening = false
                            }
                        }

                        if (sendChatButton) {
                            sendChatButton.addEventListener('click', () => {
                                const uriChat = '/ajax/chatbot/chat'

                                if (chatToken !== '' && inputField && inputField.value !== '') {
                                    const value = inputField.value
                                    inputField.value = ''
                                    appendMessage(value, false)

                                    fetch(uriChat, {
                                        method: 'POST',
                                        body: JSON.stringify({"sender_token": chatToken, "message": value})
                                    }).then(
                                        (response) => {
                                            if (response.status === 200) {
                                                return response.json()
                                            }
                                        }
                                    ).then(
                                        (data) => {
                                            if (data) {
                                                for (let i = 0; i < data.length; i++) {
                                                    const message = data[i];

                                                    if (message.text) {
                                                        appendMessage(message.text, true)
                                                    } else if (message.image) {
                                                        appendImage(message.image, true)
                                                    }

                                                }
                                            }
                                            messagesDiv.scrollTop = messagesDiv.scrollHeight
                                        }
                                    )
                                }

                            })
                        }
                    }
                }
            )
        })
    }

    if (closeChatButton) {
        closeChatButton.addEventListener('click', () => {
            if (chat && chatToken !== '') {
                const uriEndConversation = '/ajax/chatbot/end-conversation'

                fetch(uriEndConversation, {
                    method: 'POST',
                    body: JSON.stringify({"sender_token": chatToken})
                }).then(
                    (response) => {
                        if (response.status === 204) {
                            chatToken = ''
                            messagesDiv.innerHTML = ''
                        }
                    }
                )

                startChatButton.classList.remove('hidden')
                chat.classList.add('hidden')
            }
        })
    }

    const appendMessage = (text, isResponse) => {
        let newMessageDiv = document.createElement("div");
        newMessageDiv.classList.add('message')

        if (isResponse) {
            newMessageDiv.classList.add('message-left')
        } else {
            newMessageDiv.classList.add('message-right')
        }

        newMessageDiv.innerHTML = text

        messagesDiv.append(newMessageDiv)
    }

    const appendImage = (image) => {
        let newMessageDiv = document.createElement("div");
        newMessageDiv.classList.add('message')

        newMessageDiv.classList.add('message-left')

        var element = document.createElement("img");
        element.setAttribute("src", image)
        element.setAttribute("width", "100%");

        newMessageDiv.appendChild(element)

        messagesDiv.append(newMessageDiv)
    }
};
