import {Routes} from '@angular/router';
import {MetaGuard} from '@ngx-meta/core';
import {DefaultComponent} from './default/default.component';
import {IndexComponent} from './index/index.component';

export const appRoutes: Routes = [
    {
        path: '',
        children: [
            {
                path: '',
                component: IndexComponent,
            },
            {
                path: 'confirm/:token',
                loadChildren: './confirm/confirm.module#ConfirmModule',
            },
            {
                path: 'signin',
                loadChildren: './signin/signin.module#SigninModule',
            },
            {
                path: 'signup',
                loadChildren: './signup/signup.module#SignupModule',
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
