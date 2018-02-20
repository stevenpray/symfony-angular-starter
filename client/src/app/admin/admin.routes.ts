import {Routes} from '@angular/router';
import {AdminComponent} from './admin.component';
import {AdminGuard} from './admin.guard';
import {IndexComponent} from './index/index.component';

export const adminRoutes: Routes = [
    {
        path: '',
        component: AdminComponent,
        canActivate: [
            AdminGuard,
        ],
        children: [
            {
                path: '',
                component: IndexComponent,
                data: {
                    meta: {
                        title: 'Admin',
                    },
                },
            },
        ],
    },
];
