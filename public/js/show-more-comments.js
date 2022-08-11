window.addEventListener("load", init);

function init() {
    let btn = document.querySelector("#show-more-comments");
    let loader = document.querySelector(".loader-comments");
    let span = document.querySelector(".content-span-comments");

    btn.addEventListener("click", function (e) {
        btn.style.visibility = "hidden";
        loader.style.visibility = "visible";
        let comments = document.querySelectorAll(".js-line-comments");
        let lastComment = comments.length;
        const Params = new URLSearchParams();

        Params.append("offset", lastComment);
        const Url = new URL(window.location.href);
        fetch(Url.pathname + "?" + Params.toString() + "&loadComments=1", {

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

                    if (content.innerText === "Pas de commentaire trouvé") {
                        btn.style.visibility = "hidden";
                    }
                }
            );
    });
}

