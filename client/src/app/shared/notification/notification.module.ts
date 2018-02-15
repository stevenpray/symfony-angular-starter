import {HTTP_INTERCEPTORS} from '@angular/common/http';
import {NgModule} from '@angular/core';
import {NotificationComponent} from './notification.component';
import {NotificationService} from './notification.service';

@NgModule({
    declarations: [
        NotificationComponent,
    ],
    exports: [
        NotificationComponent,
    ],
    providers: [
        NotificationService,
        {provide: HTTP_INTERCEPTORS, useClass: NotificationService, multi: true},
    ],
})
export class NotificationModule {
}
