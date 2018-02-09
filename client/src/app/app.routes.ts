import {Routes} from '@angular/router';
import {DefaultComponent} from './default/default.component';
import {HomeComponent} from './home/home.component';
import {SigninComponent} from './signin/signin.component';

export const appRoutes: Routes = [
    {
        path: '',
        component: HomeComponent,
    },
    {
        path: 'signin',
        component: SigninComponent,
    },
    {
        path: '**',
        component: DefaultComponent,
    },
];
