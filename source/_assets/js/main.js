window.docsearch = require('docsearch.js');

import Prism from 'prismjs'

import 'prismjs/components/prism-markup';
import 'prismjs/components/prism-markup-templating';
import 'prismjs/components/prism-clike';
import 'prismjs/components/prism-php';
import 'prismjs/plugins/line-highlight/prism-line-highlight';

Prism.highlightAll();

window.$ = window.jQuery = require('jquery');

$(document).ready(() => {
    $('.content > table').wrap($("<div />").addClass('block shadow overflow-x-scroll rounded-lg'));
});