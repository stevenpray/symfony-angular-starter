import {HttpEvent, HttpHandler, HttpInterceptor, HttpRequest} from '@angular/common/http';
import {Injectable} from '@angular/core';
import {Observable} from 'rxjs/Rx';
import {AuthToken} from './auth-token';
import {log} from './auth.module';
import {AuthService} from './auth.service';

@Injectable()
export class AuthHttpInterceptor implements HttpInterceptor {

    public static readonly AUTHORIZATION_HEADER_NAME = 'Authorization';

    private _token: AuthToken;

    constructor(private _service: AuthService) {
        this._service.token$.subscribe((token: AuthToken) => this._token = token);
    }

    public intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        if (this._token) {
            const value = 'Bearer ' + this._token.encoded;
            request = request.clone({headers: request.headers.set(AuthHttpInterceptor.AUTHORIZATION_HEADER_NAME, value)});
            log('Authorization header appended.');
        }
        return next.handle(request);
    }
}
