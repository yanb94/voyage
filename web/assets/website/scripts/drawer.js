const drawerButton = document.getElementById('drawer-button');
const drawer = document.getElementById('drawer');
const body = document.querySelector('body');
const drawerBackground = document.querySelector('.layout--drawer--background');

drawerBackground.addEventListener('click',() => {
    drawer.classList.remove('open');
    body.classList.remove('drawer');
    drawerBackground.classList.remove('open');
});

drawerButton.addEventListener('click',() => {
    drawer.classList.toggle('open');
    body.classList.toggle('drawer');
    drawerBackground.classList.toggle('open');
});