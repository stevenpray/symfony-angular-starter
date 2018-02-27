import {CommonModule} from '@angular/common';
import {NgModule} from '@angular/core';
import {RouterModule} from '@angular/router';
import {accountRoutes} from './account-routes';
import {AccountComponent} from './account.component';
import {AccountGuard} from './account.guard';
import {AccountService} from './account.service';
import {IndexComponent} from './index/index.component';

@NgModule({
    declarations: [
        AccountComponent,
        IndexComponent,
    ],
    imports: [
        CommonModule,
        RouterModule.forChild(accountRoutes),
    ],
    providers: [
        AccountGuard,
        AccountService,
    ],
})
export class AccountModule {
}
