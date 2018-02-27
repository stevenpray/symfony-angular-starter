import {Routes} from '@angular/router';
import {ConfirmComponent} from './confirm.component';

export const confirmRoutes: Routes = [
    {
        path: '',
        component: ConfirmComponent,
        data: {
            meta: {
                title: 'Confirmation',
            },
        },
    },
];
