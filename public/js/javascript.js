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

// Fonction lors du clique sur le bouton j'aime
function onClickBtnLike(event) {
    // Pour qu'il est plus aucun evenement sur la page quand on arrive sur la fonction
    event.preventDefault();

    // Constante qui contient url de la balise "a"
    const url = this.href;
    // Constante qui contient la balise "span" qui à pour class "js-likes" en question
    const spanCount = this.querySelector('span.js-likes');
    // Constante qui contient la balise "i" en question
    const icone = this.querySelector('i');

    // Fonction Ajax sur l'url grâce à la constante
    axios.get(url).then(function (response) {
        // On insère une response data qui renvoie le nombre de likes dans la constante "spanCount"
        spanCount.innerHTML = response.data.likes;

        // Condition qui signifis que si les balises "i", contiennent la class "fas", alors on remplace par
        // "far", et dans le else ont fait l'inverse on remplace "far" par "fas"
        if (icone.classList.contains('fas')) {
            icone.classList.replace('fas', 'far');
        } else {
            icone.classList.replace('far', 'fas');
        }
        // Les cas d'erreur par rapport au status
    }).catch(function (error) {
        // Si la status d'erreur est égale à 403
        if (error.response.status === 403) {
            // Alors ça renvoie une alerte comme quoi il faut vous connecté pour liker
            window.alert("Vous ne pouvez pas liker un article si vous n'êtes pas connecté!")
        } else {
            // Else (Pour tout les autres cas d'erreur possible) ca renvoie une alerte comme quoi une erreur
            // s'est produite et qu'il faut réessayer ultérieurement (Problème de connexion ou autres, 404)
            window.alert("Une erreur s'est produite, réessayer plus tard")
        }
    });
}

// La on selection grâce à la méthode natif toutes les balises "a" contenant la class "js-like-link",
// on y applique une forEach dessus avec en paramètre "link"
document.querySelectorAll('a.js-like-link').forEach(function (link) {
    // Sur ce paramètre nous faisant on evement lorsque l'on clique qu'il exécute la fonction ci-dessus
    link.addEventListener('click', onClickBtnLike);
});