import {animate, animation, query, style, transition, useAnimation} from '@angular/animations';

export const animations = {
    fade: animation([
        style({opacity: '{{ start }}'}),
        animate('{{ duration }} ease', style({opacity: '{{ end }}'})),
    ], {params: {duration: '500ms', start: 0, end: 1}}),
};

export const routeTransition = transition('* => *', [
    query(':enter', useAnimation(animations.fade), {optional: true}),
]);
