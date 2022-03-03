
const querySelectorVille = $('.lieu-ville');

$(document).ready(function (){
    gestionAdresse()
})

querySelectorVille.change(function (){
    gestionAdresse()
})

function gestionAdresse(){
    let valueVille = querySelectorVille.val();
    $.ajax({
        type: "POST",
        url:"create",
        data: {"idville":valueVille},
        success:function (data){
            afficherLieu(data)
        }
    })
}

function afficherLieu(data){

    // Selection et vidage de la zone d'affichage de lieu
    let zoneAffichage = document.querySelector('.affichage-adresse')
    $('.listeLieux').remove()
    $('.listeLieuxSelect').remove()

    // Création de la balise Select
    let baliseSelect = document.createElement('select')
    baliseSelect.setAttribute('id','listeLieux')
    baliseSelect.setAttribute('class','listeLieuxSelect')
    baliseSelect.setAttribute('name','listeLieux')

    //création du label de la balise Select
    let labelSelect = document.createElement('label')
    labelSelect.setAttribute('for','listeLieux')
    labelSelect.setAttribute('class','listeLieux')
    labelSelect.innerText = 'Lieux : '

    //Insertion des options dans la balise select
    data.forEach(uneData => ajouterOption(uneData))

    //Insertion des deux éléments Select et label
    zoneAffichage.insertAdjacentElement('beforeend',labelSelect)
    zoneAffichage.insertAdjacentElement('beforeend',baliseSelect)

    //Appel de la fonction de d'affichage rue et Code postal du lieu
    let QSlisteLieux = $('#listeLieux')
    chargementAdresse()
    QSlisteLieux.change(function (){
        chargementAdresse()
    })

    function ajouterOption(uneData){
        let baliseOption = document.createElement('option')
        baliseOption.setAttribute('value',uneData['id'])
        baliseOption.innerText = uneData['nom']
        baliseSelect.insertAdjacentElement('beforeend',baliseOption)
    }

    function chargementAdresse(){
        let valueLieux = QSlisteLieux.val();
        $.ajax({
            type: "POST",
            url:"create",
            data: {'idlieu':valueLieux},
            success:function (data){
                afficherAdresse(data)
            }
        })
    }
    function afficherAdresse(datalieu){
        $('#div-adresse').remove()
        if(datalieu['rue'] !== undefined){
            let divAdresse = document.createElement('div')
            let divRue = document.createElement('div')
            let divCP = document.createElement('div')
            divAdresse.setAttribute('id','div-adresse')
            divRue.innerText = "Adresse : " + datalieu['rue']
            divCP.innerText = "Code Postal : " + datalieu['cp']

            divAdresse.insertAdjacentElement('beforeend', divRue)
            divAdresse.insertAdjacentElement('beforeend', divCP)
            zoneAffichage.insertAdjacentElement('beforeend', divAdresse)
        }
    }
}