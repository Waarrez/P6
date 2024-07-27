document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'ajout des commentaires
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        const url = window.location.href;
        const slugMatches = url.match(/\/trick\/detail\/([^\/]+)$/);
        const slug = slugMatches ? slugMatches[1] : '';

        commentForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const content = document.getElementById('content').value.trim();

            if (content === '') {
                alert('Veuillez entrer un commentaire.');
                return;
            }

            const xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    const response = JSON.parse(this.responseText);
                    const newComments = response.comments;

                    const commentairesDiv = document.getElementById('commentaires');
                    if (commentairesDiv) {
                        commentairesDiv.innerHTML = '<div class="alert alert-success">Votre commentaire a bien été ajouté !</div>';

                        newComments.forEach(comment => {
                            const newCommentDiv = document.createElement('div');
                            newCommentDiv.classList.add('d-flex', 'align-items-center', 'gap-5', 'mt-5');
                            newCommentDiv.style.backgroundColor = '#f7f7f9';

                            const imgSrc = comment.userPicture
                                ? '/uploads/pictures/' + comment.userPicture
                                : '/img/default-picture.png';

                            newCommentDiv.innerHTML = `
                                <img class="rounded-circle" width="5%" src="${imgSrc}" alt="Avatar" onerror="this.src='/img/default-picture.png'">
                                <span class="badge bg-dark">${comment.username}</span>
                                <p style="margin-left: 100px; margin-bottom: 0">${comment.content}</p>
                            `;

                            commentairesDiv.appendChild(newCommentDiv);

                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        });
                    }
                }
            };

            xhttp.open('POST', '/add-comment', true);
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhttp.send(`slug=${slug}&content=${encodeURIComponent(content)}`);
        });
    }

    // Gestion du chargement des commentaires
    const comments = document.getElementsByClassName("comments");
    const buttonShow = document.getElementById("loadComment");

    if (buttonShow) {
        buttonShow.addEventListener('click', () => {
            Array.from(comments).forEach(comment => comment.removeAttribute("id"));
            buttonShow.classList.add("noneButton");
        });
    }

    // Gestion de la suppression des figures
    const confirmationButtons = document.querySelectorAll(".confirmationButton");

    confirmationButtons.forEach(button => {
        button.addEventListener("click", function(event) {
            event.preventDefault();
            const confirmation = confirm("Êtes-vous sûr de supprimer cette figure ?");
            if (confirmation) {
                const idTrick = button.getAttribute('data-trick');
                window.location.href = `/trick/remove/${idTrick}`;
            }
        });
    });

    // Gestion de l'ajout des vidéos
    const collectionHolder = document.querySelector('ul.videos');
    const addVideoButton = document.getElementById('add-video-btn');

    if (collectionHolder && addVideoButton) {
        const prototype = collectionHolder.dataset.prototype;
        let index = collectionHolder.querySelectorAll('li').length;

        collectionHolder.dataset.index = index;

        addVideoButton.addEventListener('click', function(e) {
            e.preventDefault();
            addVideoForm(collectionHolder, addVideoButton);
        });

        function addVideoForm(collectionHolder, addVideoButton) {
            const prototype = collectionHolder.dataset.prototype;
            const index = collectionHolder.dataset.index;
            const newForm = prototype.replace(/__name__/g, index);
            collectionHolder.dataset.index = parseInt(index, 10) + 1;

            const newFormLi = document.createElement('li');
            newFormLi.innerHTML = newForm;
            addRemoveButton(newFormLi);
            addVideoButton.before(newFormLi);
        }

        function addRemoveButton(formLi) {
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn btn-danger mt-5 mb-5';
            removeButton.textContent = 'Supprimer cette vidéo';
            formLi.appendChild(removeButton);

            removeButton.addEventListener('click', function(e) {
                e.preventDefault();
                formLi.remove();
            });
        }

        collectionHolder.querySelectorAll('li').forEach(li => {
            addRemoveButton(li);
        });
    }

    // delete videos

    const deleteVideoButtons = document.querySelectorAll('.delete-video');

    deleteVideoButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm("Êtes-vous sûr de vouloir supprimer la vidéo ?")) {
                // Récupérer l'URL et le token à partir des attributs du bouton
                const href = this.getAttribute("href");
                const token = this.getAttribute("data-token");

                fetch(href, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': token
                    },
                    body: JSON.stringify({ "_token": token })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            // Supprimer l'élément parent avec la classe 'd-flex'
                            const parent = this.closest('.d-flex');
                            if (parent) {
                                parent.remove();
                            }
                            // Recharger la page
                            window.location.reload();
                        } else {
                            alert(data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue, veuillez réessayer.');
                    });
            }
        });
    });

    // delete images
    const removeButtons = document.querySelectorAll('[data-remove]');

    removeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm("Êtes-vous sûr de vouloir supprimer l'image ?")) {
                // Récupérer l'URL à partir de l'attribut href
                const href = this.getAttribute("href");

                fetch(href, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.success) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                const cardImgContainer = this.closest(".card-img-container");
                                if (cardImgContainer) {
                                    cardImgContainer.remove();
                                }
                            }
                        } else {
                            alert(data.error || 'Une erreur est survenue.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue, veuillez réessayer.');
                    });
            }
        });
    });

    // delete images secondary
    const deleteButtons = document.querySelectorAll('[data-delete]');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            if (confirm("Êtes-vous sûr de vouloir supprimer l'image ?")) {
                const href = button.getAttribute('href');
                const token = button.getAttribute('data-token');


                fetch(href, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ "_token": token })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            button.parentElement.remove();
                            window.location.reload();
                        } else {
                            alert(data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue, veuillez réessayer.');
                    });
            }
        });
    });


    // Load more comments
    const loadMoreButton = document.getElementById('loadMoreComments');

    if (loadMoreButton) {
        loadMoreButton.addEventListener('click', function() {
            const hiddenComments = document.querySelectorAll('.comment.hidden-comment');

            hiddenComments.forEach(function(comment) {
                comment.classList.remove('hidden-comment');
            });

            const loadMoreContainer = document.getElementById('loadMoreContainer');
            if (loadMoreContainer) {
                loadMoreContainer.style.display = 'none';
            }
        });
    }
});
