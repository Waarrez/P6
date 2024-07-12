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
            let newComments = response.comments;

            let commentairesDiv = document.getElementById('commentaires');
            commentairesDiv.innerHTML = '<div class="alert alert-success">Votre commentaire a bien été ajouté !</div>';

            newComments.forEach(function(comment) {
                let newCommentDiv = document.createElement('div');
                newCommentDiv.classList.add('d-flex', 'align-items-center', 'gap-5', 'mt-5');
                newCommentDiv.style.backgroundColor = '#f7f7f9';

                let imgSrc = comment.userPicture && comment.userPicture !== ""
                    ? '/uploads/pictures/' + comment.userPicture
                    : '/img/default-picture.png';

                newCommentDiv.innerHTML = `
                            <img class="rounded-circle" width="5%" src="${imgSrc}" alt="Avatar" onerror="this.src='/img/default-picture.png'">
                            <span class="badge bg-dark">${comment.username}</span>
                            <p style="margin-left: 100px; margin-bottom: 0">${comment.content}</p>
                        `;

                commentairesDiv.appendChild(newCommentDiv);
            });
        }
    };

    xhttp.open('POST', '/add-comment', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('slug=' + slug + '&content=' + content);
});

let comments = document.getElementsByClassName("comments");
let buttonShow = document.getElementById("loadComment");

buttonShow.addEventListener('click', () => {
    for (let i = 0; i < comments.length; i++) {
        comments[i].removeAttribute("id");
    }
    buttonShow.classList.add("noneButton");
});