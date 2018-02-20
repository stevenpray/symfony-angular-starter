import {CommonModule} from '@angular/common';
import {NgModule} from '@angular/core';
import {RouterModule} from '@angular/router';
import {AdminComponent} from './admin.component';
import {AdminGuard} from './admin.guard';
import {adminRoutes} from './admin.routes';
import {AdminService} from './admin.service';
import {IndexComponent} from './index/index.component';

@NgModule({
    declarations: [
        AdminComponent,
        IndexComponent,
    ],
    exports: [],
    imports: [
        CommonModule,
        RouterModule.forChild(adminRoutes),
    ],
    providers: [
        AdminGuard,
        AdminService,
    ],
})
export class AdminModule {
}
