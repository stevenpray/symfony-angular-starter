import {Routes} from '@angular/router';
import {IndexComponent} from './index/index.component';
import {SignupComponent} from './signup.component';

export const signupRoutes: Routes = [
    {
        path: '',
        component: SignupComponent,
        children: [
            {
                path: '',
                component: IndexComponent,
                data: {
                    meta: {
                        title: 'Sign-Up',
                    },
                },
            },
        ],
    },
];
