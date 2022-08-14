window.addEventListener("load", init);

function init() {
    let btn = document.querySelector("#show-more-users");
    let loader = document.querySelector(".loader-users");
    let span = document.querySelector(".content-span-users");

    if (btn) {
        btn.addEventListener("click", function (e) {
            btn.style.visibility = "hidden";
            loader.style.visibility = "visible";
            let users = document.querySelectorAll(".js-line-users");
            let lastUser = users.length;
            const Params = new URLSearchParams();
            Params.append("offset", lastUser);
            const Url = new URL(window.location.origin);
            fetch(Url.origin + "/manageArticles" + "?" + Params.toString() + "&loadUsers=1", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }).then(response => response.json())
                .then(
                    data => {
                        let content = document.createElement("tbody");
                        content.innerHTML = data.content;
                        span.appendChild(content);
                        btn.style.visibility = "visible";
                        loader.style.visibility = "hidden";

                        if (content.innerText === "Pas d'utilisateur trouvé.") {
                            btn.style.visibility = "hidden";
                        }
                    }
                );
        });
    }
}

