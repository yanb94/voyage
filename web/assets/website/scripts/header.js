const header =  document.querySelector('.layout--header');

if(window.scrollY > 0)
{
    header.classList.add('scroll');
}

window.addEventListener("scroll",() => {

    const scrollPos = window.scrollY;

    if(scrollPos > 0)
    {
        header.classList.add('scroll');
    }
    else
    {
        header.classList.remove('scroll');
    }
})