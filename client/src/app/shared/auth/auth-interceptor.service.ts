import {HttpEvent, HttpHandler, HttpInterceptor, HttpRequest} from '@angular/common/http';
import {Injectable} from '@angular/core';
import * as debug from 'debug';
import {Observable} from 'rxjs/Rx';
import {AuthToken} from './auth-token';
import {AuthTokenService} from './auth-token.service';

const log = debug('app:auth');

@Injectable()
export class AuthInterceptorService implements HttpInterceptor {

    public static readonly AUTHORIZATION_HEADER_NAME = 'Authorization';

    private _token: AuthToken;

    constructor(private _tokenService: AuthTokenService) {
        this._tokenService.token$.subscribe((token: AuthToken) => this._token = token);
    }

    public intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        if (this._token) {
            const value = 'Bearer ' + this._token.encoded;
            request = request.clone({headers: request.headers.set(AuthInterceptorService.AUTHORIZATION_HEADER_NAME, value)});
            log('Authorization header appended.');
        }
        return next.handle(request);
    }
}
