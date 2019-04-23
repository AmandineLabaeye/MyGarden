let Image = [
    "https://cdn.florajet.com/produits/zoom/3482.jpg",
    "https://upload.wikimedia.org/wikipedia/commons/thumb/2/22/Acajou.jpg/220px-Acajou.jpg",
    "https://jardinage.lemonde.fr/images/dossiers/historique/camellia-143321.jpg"
];

$("#Carousel").css({
    'background-image': "url(" + Image[0] + ")"
});

let i = 0;

$("#Next").click(function () {

    i++;

    if (i === Image.length) {

        i = 0;

    }

    $("#Carousel").css({
        'background-image': "url(" + Image[i] + ")"
    });
});

$("#Previous").click(function () {

    if (i === 0) {

        i = Image.length;

    }

    if (i > 0) {

        i--;

    }

    $("#Carousel").css({
        'background-image': "url(" + Image[i] + ")"
    });

});