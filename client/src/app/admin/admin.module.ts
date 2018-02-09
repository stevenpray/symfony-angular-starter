import {NgModule} from '@angular/core';
import {RouterModule} from '@angular/router';
import {AdminComponent} from './admin.component';
import {AdminGuard} from './admin.guard';
import {adminRoutes} from './admin.routes';
import {IndexComponent} from './index/index.component';

@NgModule({
    declarations: [
        AdminComponent,
        IndexComponent,
    ],
    exports: [],
    imports: [
        RouterModule.forChild(adminRoutes),
    ],
    providers: [
        AdminGuard,
    ],
})
export class AdminModule {
}
