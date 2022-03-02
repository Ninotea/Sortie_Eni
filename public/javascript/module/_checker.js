
var checkboxs, index;
checkboxs = document.getElementsByClassName("checkbox");
for (index = 0; index < checkboxs.length; ++index) {
    checkboxs[index].setAttribute('checked', 'checked');
}
console.log("exécution checker")
