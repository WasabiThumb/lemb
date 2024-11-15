// Handle the main message board page

// Maintain a map to convert user IDs to user names. Cache this map to prevent unecessary requests.
const getUserName = (() => {
    const cache = {};
    return ((id) => {
        if (id in cache) return cache[id];
        return cache[id] = fetch(`./api/user/?id=${id}`).then(async (res) => {
            const json = await res.json();
            if (!json["valid"]) return "Unknown";
            return json["name"];
        });
    });
})();

// parent is #messages
// "data" has author, content
function addMessage(parent, data) {
    const el = document.createElement("div");
    el.classList.add("message");

    const h3 = document.createElement("h3");
    getUserName(data.author).then((name) => {
        h3.innerText = name;
    }).catch(console.error);
    el.appendChild(h3);

    const p = document.createElement("p");
    p.innerText = data.content;
    el.appendChild(p);

    parent.appendChild(el);
}

function sendMessage(parent, uid, msg) {
    addMessage(parent, {
        author: uid,
        content: msg
    });
    fetch('./api/send', {
        method: "POST",
        body: msg,
        headers: {
            "Content-Type": "text/plain;encoding=UTF-8",
            "Accept": "application/json"
        }
    }).catch(console.error);
}

window.addEventListener("DOMContentLoaded", () => {
    // Find our current user ID
    const uid = parseInt(document.querySelector("script#me").textContent);
    const loggedIn = uid !== 0;

    const messages = document.querySelector("#messages");

    // Request & consume the message history
    fetch("./api/messages").then(async (res) => {
        const json = await res.json();
        if (!Array.isArray(json)) return;
        for (const msg of json) {
            addMessage(messages, msg);
        }
    });

    const formSend = document.querySelector("form#send");
    const formSendText = formSend.querySelector("input#sendText");
    if (loggedIn) {
        // Request the name of our user and use it to populate <span id="identity">
        getUserName(uid).then((name) => {
            document.querySelector("#identity").innerText = `Logged in as ${name}`;
        }).catch(console.error);

        // Make the send button work
        formSend.addEventListener("submit", (e) => {
            e.preventDefault();
            sendMessage(messages, uid, formSendText.value);
        });
    } else {
        // Make the send button report an error (must be logged in)
        formSend.addEventListener("submit", (e) => {
            e.preventDefault();
            alert("You must be logged in to send messages!");
        });
    }
});