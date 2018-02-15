import {HttpClient, HttpHeaders, HttpParams, HttpResponse} from '@angular/common/http';
import {Inject, Injectable} from '@angular/core';
import {Router, RouterStateSnapshot} from '@angular/router';
import {Observable} from 'rxjs/Observable';
import {BehaviorSubject} from 'rxjs/Rx';
import {AUTH_CONFIG, AuthConfig} from './auth-config';
import {AuthRole, AuthToken} from './auth-token';
import {log} from './auth.module';

@Injectable()
export class AuthService {

    private static readonly TOKEN_NAME = 'access_token';

    private _storage = window.sessionStorage;
    private _token: AuthToken;
    private _token$ = new BehaviorSubject<AuthToken>(this._token);

    constructor(@Inject(AUTH_CONFIG) private _config: AuthConfig,
                private _router: Router,
                private _http: HttpClient) {

        if (!this._token && this._storage.getItem(AuthService.TOKEN_NAME)) {
            log('Access token found in storage.');
            try {
                this.token = new AuthToken(this._storage.getItem(AuthService.TOKEN_NAME));
            } catch (error) {
                log('Access token is malformed. Removing token from storage.');
                this._storage.removeItem(AuthService.TOKEN_NAME);
            }
        }
    }

    public get token(): AuthToken {
        return this._token;
    }

    public set token(token: AuthToken) {
        if (token !== this._token) {
            log('Access token updated.');
            if (token) {
                if (token.isExpired) {
                    log('Access Token is expired.');
                    token = null;
                } else {
                    this._storage.setItem(AuthService.TOKEN_NAME, token.encoded);
                }
            }
            if (token == null && this._storage.getItem(AuthService.TOKEN_NAME)) {
                this._storage.removeItem(AuthService.TOKEN_NAME);
            }
            this._token = token;
            this._token$.next(this._token);
        }
    }

    public get token$(): Observable<AuthToken> {
        return this._token$.distinctUntilChanged().share();
    }

    public get isAuthenticated(): boolean {
        return this.token && !this.token.isExpired;
    }

    public isAuthorized(role: AuthRole, state?: RouterStateSnapshot): boolean {
        log('Authorization required. Role "%s" required to access "%s".', AuthRole[role], state.url);
        if (this.token == null) {
            log('Authentication required.');
            if (state) {
                log('Redirecting to "%s" for interactive authentication.', this._config.urls.redirects.unauthenticated);
                this._router.navigate([this._config.urls.redirects.unauthenticated, {url: state.url}]);
            }
            return false;
        }
        const authorized = this.token.hasRole(role);
        if (authorized) {
            log('Authorization success.');
        } else {
            log('Authorization failure.');
            this._router.navigateByUrl(this._config.urls.redirects.unauthorized);
        }

        return authorized;
    }

    public login(username: string, password: string): Observable<boolean> {
        log('Authenticating user.');
        return this._http.request<any>('POST', this._config.urls.endpoint + '/login_check', {
                       body: new HttpParams().set('username', username).set('password', password).toString(),
                       headers: new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded'),
                       observe: 'response',
                   })
                   .catch(error => {
                       log('Authentication failure.');
                       throw error;
                   })
                   .do((response: HttpResponse<any>) => {
                       log('Authentication success.');
                       this.token = new AuthToken(response.body.token);
                   })
                   .map(token => this.isAuthenticated);
    }

    public logout(redirect = true): void {
        log('Clearing authentication.');
        this.token = null;
        if (redirect) {
            this._router.navigate([
                this._config.urls.redirects.unauthenticated,
                {url: this._router.routerState.snapshot.url},
            ]);
        }
    }
}
