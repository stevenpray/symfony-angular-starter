import {NgModule} from '@angular/core';
import {MatButtonModule, MatSnackBarModule, MatToolbarModule} from '@angular/material';
import {BrowserModule} from '@angular/platform-browser';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {RouterModule} from '@angular/router';
import {ServiceWorkerModule} from '@angular/service-worker';
import {MetaModule} from '@ngx-meta/core';
import {RestangularModule} from 'ngx-restangular';
import {environment} from '../environments/environment';
import {appRoutes} from './app-routes';
import {AppComponent} from './app.component';
import {authConfig, metaConfig, restangularConfig} from './app.providers';
import {DefaultComponent} from './default/default.component';
import {IndexComponent} from './index/index.component';
import {ActivityModule} from './shared/activity';
import {AuthModule} from './shared/auth';
import {NotificationModule} from './shared/notification';

@NgModule({
    declarations: [
        AppComponent,
        DefaultComponent,
        IndexComponent,
    ],
    imports: [
        ActivityModule,
        AuthModule.forRoot(authConfig),
        BrowserAnimationsModule,
        BrowserModule,
        MatButtonModule,
        MatSnackBarModule,
        MatToolbarModule,
        MetaModule.forRoot(metaConfig),
        NotificationModule,
        RestangularModule.forRoot(restangularConfig),
        RouterModule.forRoot(appRoutes),
        ServiceWorkerModule.register('/ngsw-worker.js', {enabled: environment.production}),
    ],
    bootstrap: [
        AppComponent,
    ],
})
export class AppModule {
}
