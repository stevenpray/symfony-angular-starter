import {NgModule} from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';
import {AppComponent} from './app.component';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {environment} from '../environments/environment';
import {ServiceWorkerModule} from '@angular/service-worker';
import {RouterModule} from '@angular/router';
import {appRoutes} from './app.routes';
import {DefaultComponent} from './default/default.component';
import {HomeComponent} from './home/home.component';
import {
    MatButtonModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatSnackBarModule,
    MatToolbarModule,
} from '@angular/material';
import {AuthModule} from './shared/auth';
import {HTTP_INTERCEPTORS} from '@angular/common/http';
import {MetaModule} from '@ngx-meta/core';
import {appMetaProvider} from './app.providers';
import {RestangularModule} from 'ngx-restangular';
import {SigninComponent} from './signin/signin.component';
import {ReactiveFormsModule} from '@angular/forms';
import {AppHttpInterceptorService} from './app-http-interceptor';
import {ActivityModule} from './shared/activity';
import {NotificationModule} from './shared/notification';
import {AdminModule} from './admin/admin.module';

@NgModule({
    declarations: [
        AppComponent,
        DefaultComponent,
        HomeComponent,
        SigninComponent,
    ],
    imports: [
        ActivityModule,
        AdminModule,
        AuthModule.forRoot({urls: {endpoint: environment.urls.api, redirects: {unauthenticated: 'signin', unauthorized: 'signin'}}}),
        BrowserAnimationsModule,
        BrowserModule,
        MatButtonModule,
        MatCardModule,
        MatFormFieldModule,
        MatInputModule,
        MatSnackBarModule,
        MatToolbarModule,
        MetaModule.forRoot(appMetaProvider),
        NotificationModule,
        ReactiveFormsModule,
        RestangularModule.forRoot((restangularProvider) => restangularProvider.setBaseUrl(environment.urls.api)),
        RouterModule.forRoot(appRoutes),
        ServiceWorkerModule.register('/ngsw-worker.js', {enabled: environment.production}),
    ],
    providers: [
        {provide: HTTP_INTERCEPTORS, useClass: AppHttpInterceptorService, multi: true},
    ],
    bootstrap: [AppComponent],
})
export class AppModule {
}
