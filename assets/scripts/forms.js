// The login & register forms work in a similar way, so their shared code is placed here.

/**
 * Binds a login or register form, making it work.
 * @param form {HTMLFormElement} The form element
 * @param endpoint {string} The URL to contact to make the request
 * @param validateFormData { ((fd: FormData) => string | null) | undefined } A custom function to validate form data,
 * returning an error as a string or null if no error.
 */
function bindForm(form, endpoint, validateFormData) {
    const errorSpan = form.querySelector("[data-role='error']");
    let showError;
    if (!!errorSpan) {
        errorSpan.style.opacity = "0";
        errorSpan.style.color = "red";
        showError = ((msg) => {
            errorSpan.innerText = msg;
            errorSpan.style.opacity = "1";
        });
    } else {
        showError = (() => {});
    }

    async function doSubmit(formData) {
        const response = await fetch(endpoint, {
            method: "POST",
            body: formData
        });
        if (response.status < 200 || response.status > 299) {
            showError("HTTP Error " + response.status);
            return;
        }
        const json = await response.json();
        if ("error" in json) {
            showError(json["error"]);
            return;
        }
        // go back to home page
        window.location = "../";
    }

    let requestPending = false;
    function submit(formData) {
        if (requestPending) return;
        requestPending = true;
        doSubmit(formData).then(() => {
            requestPending = false;
        }).catch((e) => {
            console.error(e);
            showError("Unexpected error");
            requestPending = false;
        });
    }

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const fd = new FormData(form);
        if (!!validateFormData) {
            const err = validateFormData(fd);
            if (err !== null) {
                showError(err);
                return;
            }
        }
        submit(fd);
    });
}
