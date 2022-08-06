/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
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

