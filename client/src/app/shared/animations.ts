import {animate, animation, query, style, transition, useAnimation} from '@angular/animations';

export const animations = {
    fadeIn: animation([
        style({opacity: '{{ start }}'}),
        animate('{{ duration }} {{ timing }}', style({opacity: '{{ end }}'})),
    ], {params: {duration: '300ms', timing: 'cubic-bezier(0.35, 0, 0.25, 1)', start: 0, end: 1}}),
};

export const routeTransition = transition('* => *', [
    query(':enter', useAnimation(animations.fadeIn), {optional: true}),
]);


