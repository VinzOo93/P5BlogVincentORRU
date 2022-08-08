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
        console.log(lastArticles);
        Params.append("offset", lastArticles);
        const Url = new URL(window.location.href);
        console.log(Url.pathname + "?" + Params.toString() + "&loadArticle=1");
        fetch(Url.pathname + "?" + Params.toString() + "&loadArticle=1", {

            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        }).then(response => response.json())
            .then(
                data => {
                    console.log(data)
                    let content = document.createElement("div");
                    content.innerHTML = data.content;
                    span.appendChild(content);
                    btn.style.visibility = "visible";
                    loader.style.visibility = "hidden";

                    if (content.innerText === "Pas d'article trouvé") {
                        btn.style.visibility = "hidden";
                    }
                }
            );
    });
}

