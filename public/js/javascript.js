/**
 * Tableau avec la liste des images à afficher dans le caroussel
 */
let Image = [
    "https://cdn.florajet.com/produits/zoom/3482.jpg",
    "https://upload.wikimedia.org/wikipedia/commons/thumb/2/22/Acajou.jpg/220px-Acajou.jpg",
    "https://jardinage.lemonde.fr/images/dossiers/historique/camellia-143321.jpg"
];

/**
 * Ligne en "jQuery", qui défini le "background-image" par défaut du caroussel, soit la première image
 */
$("#Carousel").css({
    'background-image': "url(" + Image[0] + ")"
});
/**
 * Variable qui défini l'image par défaut, "0" = la première entrée du tableau
 */
let i = 0;

/**
 * Eléments HTML écouteur d'un événement de click qui précise qu'à chaque clique sur l'éléments
 */
$("#Next").click(function () {

    /**
     * A chaque clique la variable "i" augmente
     */
    i++;

    /**
     * Conditions qui signifie que l'orsque la variable "i" est strictement égale à la longueur du tableau
     */
    if (i === Image.length) {

        /**
         * Cette variable doit retourner à 0
         */
        i = 0;

    }

    /**
     * Ligne en "jQuery", qui défini le "background-image" du caroussel en fonction de la valeur de la variable "i"
     */
    $("#Carousel").css({
        'background-image': "url(" + Image[i] + ")"
    });
});

/**
 * Eléments HTML écouteur d'un événement de click qui précise qu'à chaque clique sur l'éléments
 */
$("#Previous").click(function () {

    /**
     * Conditions qui signifie que l'orsque la variable "i" est strictement égale à "0"
     */
    if (i === 0) {
        /**
         * Cette variable doit signifier la longueur du tableau "Image"
         */
        i = Image.length;

    }

    /**
     * Conditions qui signifie que tant que la variable "i" est supérieur à 0
     */
    if (i > 0) {

        /**
         * A chaque clique la variable "i" diminue
         */
        i--;

    }

    /**
     * Ligne en "jQuery", qui défini le "background-image" du caroussel en fonction de la valeur de la variable "i"
     */
    $("#Carousel").css({
        'background-image': "url(" + Image[i] + ")"
    });

});

function onClickBtnLike(event) {
    event.preventDefault();

    const url = this.href;
    const spanCount = this.querySelector('span.js-likes');
    const icone = this.querySelector('i');

    axios.get(url).then(function (response) {
        console.log(response);
        spanCount.innerHTML = response.data.likes;

        if (icone.classList.contains('fas')) {
            icone.classList.replace('fas', 'far');
        } else {
            icone.classList.replace('far', 'fas');
        }
    }).catch(function (error) {
        if (error.response.status === 403) {
            window.alert("Vous ne pouvez pas liker un article si vous n'êtes pas connecté!")
        } else {
            window.alert("Une erreur s'est produite, réessayer plus tard")
        }

    });
}

document.querySelectorAll('a.js-like-link').forEach(function (link) {
    link.addEventListener('click', onClickBtnLike);
});