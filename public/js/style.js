//Ici on crée 2 évenements lors d'un clic sur le menu burger
var mBurger = document.querySelector('.m-nav-toggle')
mBurger.addEventListener('click', function(e) {
    // On fait apparaître la class "open" lors du clic sur le menu burger
    var mRight = document.querySelector('.m-right')
    mRight.classList.toggle('open')
    
    // On fait apparaître la class "open" lors du clic sur le menu burger pour ensuite
    // pouvoir faire une transition du menu burger en css
    var mToggle = document.querySelector('.m-nav-toggle')
    mToggle.classList.toggle('open')
    e.preventDefault()
})


// Page vacabulary word list: affichage des definition au clic
var elementOnClick = document.querySelectorAll('.word-list-container')
    for(i = 0; i < elementOnClick.length; i++) {

        var allElements = elementOnClick[i] 
        var definitionAppearance = function (e) {
            
            var definition = this.nextElementSibling
            definition.classList.toggle('onclick')
            e.stopPropagation()
        }
        allElements.addEventListener('click', definitionAppearance)
    }

    
// Page vacabulary word list: affichage des definition au clic
var elementOnClick = document.querySelectorAll('.capitals-list-container')
for(i = 0; i < elementOnClick.length; i++) {

    var allElements = elementOnClick[i] 
    var definitionAppearance = function (e) {
        
        var definition = this.nextElementSibling
        definition.classList.toggle('onclick')
        e.stopPropagation()
    }
    allElements.addEventListener('click', definitionAppearance)
}

var elementAppear = document.querySelector('.answer-button')
var parent = document.querySelector('.container-vocabulary-word')
elementAppear.addEventListener('click', function (e) {
        definitionReveal = document.querySelector('.answer') 
        definitionReveal.classList.add('appear') 
        parent.removeChild(elementAppear)
        e.preventDefault      
    })




