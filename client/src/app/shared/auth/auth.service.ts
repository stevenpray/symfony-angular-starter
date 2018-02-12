import {HttpClient, HttpErrorResponse, HttpHeaders, HttpParams, HttpResponse,} from '@angular/common/http';
import {Inject, Injectable} from '@angular/core';
import {Router, RouterStateSnapshot} from '@angular/router';
import * as debug from 'debug';
import * as moment from 'moment';
import {Observable, Subject, Subscription} from 'rxjs/Rx';
import {AUTH_CONFIG, AuthConfig} from './auth-config';
import {AuthRole} from './auth-role';
import {AuthToken} from './auth-token';
import {AuthTokenService} from './auth-token.service';

const log = debug('app:auth');

@Injectable()
export class AuthService {

    private static readonly TOKEN_REFRESH_MIN_TTL = 600000;
    private static readonly TOKEN_REFRESH_INTERVAL = 10000;

    private _token: AuthToken;
    private _refreshTimerSubscription: Subscription;
    private _tokenSubscription: Subscription;

    constructor(@Inject(AUTH_CONFIG) private _config: AuthConfig | undefined,
                private _router: Router,
                private _http: HttpClient,
                private _tokenService: AuthTokenService) {

        this._tokenSubscription = this._tokenService.token$.subscribe((token: AuthToken) => {
            if (token) {
                const ttl: number = token.expiration.getTime() - new Date().getTime();
                log('Access token will expire %s.', moment().add(ttl, 'ms').fromNow());
                const ttr = ttl < AuthService.TOKEN_REFRESH_MIN_TTL + 1000 ? 0 : ttl - AuthService.TOKEN_REFRESH_MIN_TTL;
                log('Access token will refresh %s.', moment().add(ttr, 'ms').fromNow());
                const refreshTimer = Observable.timer(ttr, AuthService.TOKEN_REFRESH_INTERVAL);
                this._refreshTimerSubscription = refreshTimer.subscribe(
                    (value: number) => {
                        log('Access token refreshing.');
                        this.refresh().subscribe(
                            (response: HttpResponse<any>) => {
                                log('Access token refresh success.');
                                this.token = new AuthToken(response.body);
                            },
                            (error: HttpErrorResponse) => {
                                log('Access token refresh failure.');
                                if (error.status === 403) {
                                    this.logout();
                                }
                            },
                        );
                    },
                );
            } else if (this._refreshTimerSubscription && this._refreshTimerSubscription.closed === false) {
                this._refreshTimerSubscription.unsubscribe();

            }
        });
    }

    public get token$(): Observable<AuthToken> {
        return this._tokenService.token$;
    }

    private set token(token: AuthToken) {
        if (this._token !== token) {
            if (this._refreshTimerSubscription) {
                this._refreshTimerSubscription.unsubscribe();
            }
            this._token = token;
            this._tokenService.token = token;
        }
    }

    public authorize(role: AuthRole, state?: RouterStateSnapshot): boolean {
        log('Authorization required. Role "%s" required to access "%s".', AuthRole[role], state.url);
        const token = this._tokenService.token;
        if (token == null) {
            log('Authentication required.');
            if (state) {
                log('Redirecting to interactive signin: "%s"', this._config.urls.redirects.unauthenticated);
                this._router.navigate([this._config.urls.redirects.unauthenticated, {url: state.url}]);
            }
            return false;
        }
        const authorized = token.user.hasRole(role);
        if (authorized) {
            log('Authorization success.');
        } else {
            log('Authorization failure.');
            this._router.navigateByUrl(this._config.urls.redirects.unauthorized);
        }

        return authorized;
    }

    public refresh(): Observable<HttpResponse<any>> {
        return this._request<any>('GET', '/refresh');
    }

    public password(username: string): Observable<HttpResponse<any>> {
        return this._request<any>('POST', '/password', username);
    }

    public username(email: string): Observable<any> {
        return this._request<any>('POST', '/username', email);
    }

    public confirm(token: string, password: { password: string, password_confirm: string }): Observable<HttpResponse<any>> {
        return this._request<any>('POST', '/confirm/' + token, {plain_password: password});
    }

    public login(username: string, password: string): Observable<HttpResponse<any>> {
        log('Authenticating user.');
        const source = new Subject<HttpResponse<any>>();
        const body = new HttpParams().set('username', username).set('password', password).toString();
        const headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
        this._http.request<any>('POST', this._config.urls.endpoint + '/login_check', {body: body, headers: headers, observe: 'response'})
            .subscribe(
                (response: HttpResponse<any>) => {
                    log('Authentication success.');
                    this.token = new AuthToken(response.body.token);
                    source.next(response);
                },
                (error: HttpErrorResponse) => {
                    log('Authentication failure.');
                    source.error(error);
                },
                () => source.complete(),
            );

        return source.asObservable();
    }

    public logout(redirect = true): void {
        log('Logging out user.');
        this.token = null;
        if (redirect) {
            this._router.navigate([this._config.urls.redirects.unauthenticated, {url: this._router.routerState.snapshot.url}]);
        }
    }

    private _request<R>(method: string, url: string, data?, headers?: HttpHeaders): Observable<HttpResponse<R>> {
        url = this._config.urls.endpoint + url;
        const body = data === null ? null : JSON.stringify(data);
        if (headers == null) {
            headers = new HttpHeaders();
        }
        headers.append('Accept', 'application/json');
        headers.append('Cache-Control', 'no-cache');
        return this._http.request<R>(method, url, {body: body, headers: headers, observe: 'response'})
            .catch((error: any) => {
                if (error instanceof HttpErrorResponse) {
                    const response = new HttpErrorResponse({
                        error: JSON.parse(error.error),
                        headers: error.headers,
                        status: error.status,
                        statusText: error.statusText,
                        url: error.url
                    });
                    return Observable.throw(response);
                }
                return Observable.throw(error);
            });
    }
}
