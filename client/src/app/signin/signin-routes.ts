import {Routes} from '@angular/router';
import {PasswordComponent} from './help/password/password.component';
import {UsernameComponent} from './help/username/username.component';
import {IndexComponent} from './index/index.component';
import {SigninComponent} from './signin.component';

export const signinRoutes: Routes = [
    {
        path: '',
        component: SigninComponent,
        children: [
            {
                path: '',
                component: IndexComponent,
                data: {
                    meta: {
                        title: 'Sign-In',
                    },
                },
            },
            {
                path: 'help/password',
                component: PasswordComponent,
                data: {
                    meta: {
                        title: 'Forgot Password',
                    },
                },
            },
            {
                path: 'help/username',
                component: UsernameComponent,
                data: {
                    meta: {
                        title: 'Forgot Username',
                    },
                },
            },
        ],
    },
];
