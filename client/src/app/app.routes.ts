import {Routes} from '@angular/router';
import {MetaGuard} from '@ngx-meta/core';
import {DefaultComponent} from './default/default.component';
import {HomeComponent} from './home/home.component';
import {SigninComponent} from './signin/signin.component';

export const appRoutes: Routes = [
    {
        path: '',
        children: [
            {
                path: '',
                component: HomeComponent,
            },
            {
                path: 'signin',
                component: SigninComponent,
            },
            {
                path: 'account',
                loadChildren: './account/account.module#AccountModule',
            },
            {
                path: 'admin',
                loadChildren: './admin/admin.module#AdminModule',
            },
            {
                path: '**',
                component: DefaultComponent,
            },
        ],
        canActivateChild: [
            MetaGuard,
        ],
        data: {
            i18n: {
                isRoot: true,
            },
        },
    },
];
