
const champNom = $('#sortie_nom')
const champSortie = $('#sortie_infosSortie')
const champDuree = $('#sortie_dureeH')
const champNbInscri = $('#sortie_nbInscriptionsMax')
const champDateDeb = $('#sortie_dateHeureDebut')
const champDateFinInscr = $('#sortie_dateLimiteInscription')
const champLieux = $('#listeLieux')

const btnEnregistrer = $('#sortie_enregistrer')
const btnPublier = $('#sortie_publier')

function verifierChamps(event) {
    let erreur = false;
    let message = ""
    let DDJ = new Date()
    let dateFinInscription = new Date(champDateFinInscr.val())
    let dateDebut = new Date(champDateDeb.val())

    if(champNom.val().length < 5 ){
        message += 'Le champs "Nom" doit comporter au moins 5 caractères.\n \n'
        erreur = true
    }
    if(champSortie.val().length < 1){
        message += 'Le champs "Infos sortie" doit etre completé d\'une description .\n \n'
        erreur = true
    }
    if(champDuree.val() === "00:00"){
        message += 'Merci de renseigner une durée pour votre sortie.\n \n'
        erreur = true
    }
    if(champNbInscri.val() < 1){
        message += 'Vous devez renseigner un nombre de participant maximum.\n \n'
        erreur = true
    }
    if(dateDebut < dateFinInscription){
        message += 'Renseignez une date d\'inscription antérieur à la date de début de la sortie.\n \n'
        erreur = true
    }
    if(dateDebut < DDJ){
        message += 'Renseignez une date et une heure de début de sortie utlérieur à la date et heure du jour.\n \n'
        erreur = true
    }
    if(dateDebut < DDJ){
        message += 'Renseignez une date et une heure de début de sortie utlérieur à la date et heure du jour.\n \n'
        erreur = true
    }


    if(erreur){
        alert(message)
        event.preventDefault();
    }
}

btnPublier.click(function (event) {
    verifierChamps(event);
})
btnEnregistrer.click(function (event) {
    verifierChamps(event);
})


