window.addEventListener("load", init);

function init() {
    let btn = document.querySelector("#show-more-articles");
    let loader = document.querySelector(".loader-articles");
    let span = document.querySelector(".content-span-articles");

    btn.addEventListener("click", function (e) {
        btn.style.visibility = "hidden";
        loader.style.visibility = "visible";
        let articles = document.querySelectorAll(".js-card-articles");
        let lastArticles = articles.length;
        const Params = new URLSearchParams();
        Params.append("offset", lastArticles);
        const Url = new URL(window.location.origin);

        fetch(Url.origin + "/manageArticles" + "?" + Params.toString() + "&loadArticle=1", {

            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        }).then(response => response.json())
            .then(
                data => {
                    let content = document.createElement("div");
                    content.innerHTML = data.content;
                    span.appendChild(content);
                    btn.style.visibility = "visible";
                    loader.style.visibility = "hidden";

                    if (content.innerText === "Pas d'article trouvé.") {
                        btn.style.visibility = "hidden";
                    }
                }
            );
    });
}

