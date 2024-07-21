let url = window.location.href;
let slugMatches = url.match(/\/trick\/detail\/([^\/]+)$/);
let slug = slugMatches && slugMatches[1];

document.getElementById('commentForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const content = document.getElementById('content').value.trim();

    if (content === '') {
        alert('Veuillez entrer un commentaire.');
        return;
    }

    let xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            let response = JSON.parse(this.responseText);

            if (response.success) {
                // Affiche un message de succès temporaire
                let successMessage = document.createElement('div');
                successMessage.classList.add('alert', 'alert-success');
                successMessage.textContent = 'Votre commentaire a bien été ajouté !';
                document.getElementById('commentaires').appendChild(successMessage);

                window.location.reload();
            } else {
                alert('Une erreur est survenue. Veuillez réessayer.');
            }
        }
    };

    xhttp.open('POST', '/add-comment', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('slug=' + encodeURIComponent(slug) + '&content=' + encodeURIComponent(content));
});

let comments = document.getElementsByClassName("comments");
let buttonShow = document.getElementById("loadComment");

buttonShow.addEventListener('click', () => {
    for (let i = 0; i < comments.length; i++) {
        comments[i].removeAttribute("id");
    }
    buttonShow.classList.add("noneButton");
});
