import {animate, animation, query, style, transition, useAnimation} from '@angular/animations';

export const fade = animation([
    style({opacity: '{{ start }}'}),
    animate('{{ time }} ease', style({opacity: '{{ end }}'})),
], {params: {time: '300ms', start: 0, end: 1}});

export const routeTransition = transition('* => *', [
    query(':enter', useAnimation(fade), {optional: true}),
]);
