import {HTTP_INTERCEPTORS} from '@angular/common/http';
import {NgModule} from '@angular/core';
import {MatProgressBarModule} from '@angular/material';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import * as debug from 'debug';
import {ActivityComponent} from './activity.component';
import {ActivityService} from './activity.service';

export const log = debug('app:activty');

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
