// /advo/assets/js/chat.js
document.addEventListener("DOMContentLoaded", function () {
    const chatBox = document.getElementById("chatBox");
    const messageForm = document.getElementById("messageForm");

    if (!messageForm || !chatBox) {
        // nothing selected yet
        return;
    }

    // The form contains hidden inputs with names appointment_id and chat_with
    const appointmentInput = document.getElementById("appointment_id");
    const chatWithInput = document.getElementById("chat_with");

    if (!appointmentInput || !chatWithInput) {
        console.warn("Chat identifiers missing in DOM");
        return;
    }

    function getAppointment() { return appointmentInput.value; }
    function getChatWith() { return chatWithInput.value; }

    function loadMessages() {
        const a = encodeURIComponent(getAppointment());
        const c = encodeURIComponent(getChatWith());
        if (!a || !c) {
            chatBox.innerHTML = "<p class='no-messages'>Select a user to start chatting.</p>";
            return;
        }
        fetch(`/advo/chat/get_message.php?appointment_id=${a}&chat_with=${c}`)
            .then(r => r.text())
            .then(html => {
                chatBox.innerHTML = html;
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(err => console.error("Load messages error:", err));
    }

    // initial
    loadMessages();
    // poll
    setInterval(loadMessages, 2000);

    // submit handler: use FormData(messageForm) so hidden inputs + file are included
    messageForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const fd = new FormData(messageForm);
        fetch("/advo/chat/send_message.php", {
            method: "POST",
            body: fd
        })
        .then(r => r.json())
        .then(json => {
            if (json.status === "success") {
                // clear text and file input
                const msg = messageForm.querySelector('input[name="message"]');
                if (msg) msg.value = "";
                const file = messageForm.querySelector('input[type="file"]');
                if (file) file.value = "";
                loadMessages();
            } else {
                console.error("Send failed:", json.message);
                alert("Send failed: " + (json.message || "Unknown error"));
            }
        })
        .catch(err => {
            console.error("Send error:", err);
            alert("Network error while sending message");
        });
    });
});
