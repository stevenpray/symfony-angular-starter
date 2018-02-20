import {NgModule} from '@angular/core';
import {ReactiveFormsModule} from '@angular/forms';
import {MatButtonModule, MatCardModule, MatFormFieldModule, MatInputModule, MatSnackBarModule, MatToolbarModule} from '@angular/material';
import {BrowserModule} from '@angular/platform-browser';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {RouterModule} from '@angular/router';
import {ServiceWorkerModule} from '@angular/service-worker';
import {MetaModule} from '@ngx-meta/core';
import {RestangularModule} from 'ngx-restangular';
import {environment} from '../environments/environment';
import {AppComponent} from './app.component';
import {authConfig, metaConfig, restangularConfig} from './app.providers';
import {appRoutes} from './app.routes';
import {DefaultComponent} from './default/default.component';
import {HomeComponent} from './home/home.component';
import {ActivityModule} from './shared/activity';
import {AuthModule} from './shared/auth';
import {NotificationModule} from './shared/notification';
import {SigninComponent} from './signin/signin.component';

@NgModule({
    declarations: [
        AppComponent,
        DefaultComponent,
        HomeComponent,
        SigninComponent,
    ],
    imports: [
        ActivityModule,
        AuthModule.forRoot(authConfig),
        BrowserAnimationsModule,
        BrowserModule,
        // MatAutocompleteModule,
        MatButtonModule,
        // MatButtonToggleModule,
        MatCardModule,
        // MatDatepickerModule,
        // MatDialogModule,
        MatFormFieldModule,
        // MatGridListModule,
        // MatIconModule,
        MatInputModule,
        // MatListModule,
        // MatMenuModule,
        // MatNativeDateModule,
        // MatProgressSpinnerModule,
        // MatRadioModule,
        // MatRippleModule,
        // MatSelectModule,
        // MatSidenavModule,
        // MatSliderModule,
        // MatSlideToggleModule,
        MatSnackBarModule,
        // MatTabsModule,
        MatToolbarModule,
        // MatTooltipModule,
        MetaModule.forRoot(metaConfig),
        NotificationModule,
        ReactiveFormsModule,
        RestangularModule.forRoot(restangularConfig),
        RouterModule.forRoot(appRoutes),
        ServiceWorkerModule.register('/ngsw-worker.js', {enabled: environment.production}),
    ],
    providers: [],
    bootstrap: [
        AppComponent,
    ],
})
export class AppModule {
}
