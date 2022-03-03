
const btnAjouterLieu = $('#btn_ajouterLieu')
const formAjoutLieu = $('#form-ajout-lieu')
const btnFermerAjoutLieu = $('#fermer_ajout')
const corpDeFormSortie = $('#corp')
const btnSubmit = $('#smt-ajouter-lieu')


formAjoutLieu.detach()

btnAjouterLieu.click(function (event){
    event.preventDefault();
    ouvrirFormulaire()
})

btnFermerAjoutLieu.click(function (event){
    event.preventDefault();
    fermerFormulaire()
})

btnSubmit.click(function (event){
    event.preventDefault();
    ajouterLieu()
})

function ouvrirFormulaire(){
    formAjoutLieu.appendTo("main")
    corpDeFormSortie.detach()
}

function fermerFormulaire(){
    corpDeFormSortie.appendTo("main")
    formAjoutLieu.detach()
}

function ajouterLieu(){
    let villeLieu = $('.FALville')
    let nomLieu = $('.FALnom')
    let rueLieu = $('.FALrue')
    let latLieu = $('.FALlat')
    let longLieu = $('.FALlong')
    let datasend = {'ville':villeLieu.val(),
        'nom':nomLieu.val(),
        'rue':rueLieu.val(),
        'lat':latLieu.val(),
        'long':longLieu.val(),
        'ajout':"ok"}
    $.ajax({
        type: "POST",
        url:"create",
        data: datasend,
        success:function (data){
            console.log(data);
            fermerFormulaire();
            gestionAdresse();
        }
    })
}

function unused(){
}