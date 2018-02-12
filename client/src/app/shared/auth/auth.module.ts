import {HTTP_INTERCEPTORS, HttpClientModule} from '@angular/common/http';
import {ModuleWithProviders, NgModule} from '@angular/core';
import {RouterModule} from '@angular/router';
import {AUTH_CONFIG, AuthConfig} from './auth-config';
import {AuthHttpInterceptor} from './auth-http-interceptor';
import {AuthTokenService} from './auth-token.service';
import {AuthService} from './auth.service';

@NgModule({
    imports: [
        HttpClientModule,
        RouterModule,
    ],
})
export class AuthModule {

    public static forRoot(config: AuthConfig): ModuleWithProviders {
        return {
            ngModule: AuthModule,
            providers: [
                {provide: AUTH_CONFIG, useValue: config},
                {provide: HTTP_INTERCEPTORS, useClass: AuthHttpInterceptor, multi: true},
                AuthService,
                AuthTokenService,
            ],
        };
    }
}
