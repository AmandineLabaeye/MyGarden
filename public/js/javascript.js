let Image = [
    "https://cdn.florajet.com/produits/zoom/3482.jpg",
    "https://upload.wikimedia.org/wikipedia/commons/thumb/2/22/Acajou.jpg/220px-Acajou.jpg",
    "https://jardinage.lemonde.fr/images/dossiers/historique/camellia-143321.jpg"
];

document.getElementById("Carousel").style.backgroundImage =  "url("+Image[0]+")";

let i = 0;

document.getElementById("Next").addEventListener("click", function () {

    i++;

    if (i === Image.length) {

        i = 0;

    }

    document.getElementById("Carousel").style.backgroundImage =  "url("+Image[i]+")";

});

document.getElementById("Previous").addEventListener("click", function () {

    if (i === 0) {

        i = Image.length;

    }

    if (i > 0) {

        i--;

    }

    document.getElementById("Carousel").style.backgroundImage =  "url("+Image[i]+")";

});