import {Routes} from '@angular/router';
import {AccountComponent} from './account.component';
import {AccountGuard} from './account.guard';
import {IndexComponent} from './index/index.component';

export const accountRoutes: Routes = [
    {
        path: '',
        component: AccountComponent,
        canActivate: [
            AccountGuard,
        ],
        children: [
            {
                path: '',
                component: IndexComponent,
                data: {
                    meta: {
                        title: 'Account',
                    },
                },
            },
        ],
    },
];
