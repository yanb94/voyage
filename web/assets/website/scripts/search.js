const searchCont = document.querySelector('.layout--search');
const searchButton = document.querySelector('.layout--header--search-button');
const body = document.querySelector('body');

const searchClose = document.querySelector('.layout--search--close');

const layoutContent = document.querySelector('.layout--content');

searchButton.addEventListener('click', () => {
    searchCont.classList.add('open');
    body.classList.add('search');
    layoutContent.classList.add('search');
})

searchClose.addEventListener('click',() => {
    searchCont.classList.remove('open');
    body.classList.remove('search');
    layoutContent.classList.remove('search');
})