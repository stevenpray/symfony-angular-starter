import {HTTP_INTERCEPTORS} from '@angular/common/http';
import {NgModule} from '@angular/core';
import {MatProgressBarModule} from '@angular/material';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {ActivityComponent} from './activity.component';
import {ActivityService} from './activity.service';

@NgModule({
    declarations: [
        ActivityComponent,
    ],
    exports: [
        ActivityComponent,
    ],
    imports: [
        BrowserAnimationsModule,
        MatProgressBarModule,
    ],
    providers: [
        ActivityService,
        {provide: HTTP_INTERCEPTORS, useClass: ActivityService, multi: true},
    ],
})
export class ActivityModule {
}
