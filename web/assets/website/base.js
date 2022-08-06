import "@fortawesome/fontawesome-free/css/all.min.css";
import "@fortawesome/fontawesome-free/js/all.js";


import './scripts/header';
import './scripts/drawer';
import './scripts/search';
import './scripts/answerForm';
import {ellipsis} from "ellipsed";

window.onload = () => {
    ellipsis(".article-item--content--title",1,{
        responsive: true
    })

    ellipsis(".article-item--content--desc",4,{
        responsive: true
    })
}



// start the Stimulus application
import './bootstrap';