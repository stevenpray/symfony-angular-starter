import {Inject, Injectable} from '@angular/core';
import * as debug from 'debug';
import {BehaviorSubject, Observable} from 'rxjs/Rx';
import {AUTH_CONFIG, AuthConfig} from './auth-config';
import {AuthToken} from './auth-token';

const log = debug('app:auth');

@Injectable()
export class AuthTokenService {

    private static readonly TOKEN_NAME = 'access_token';

    private _storage: Storage;
    private _token: AuthToken;
    private _token$ = new BehaviorSubject<AuthToken>(this._token);

    constructor(@Inject(AUTH_CONFIG) private _config:  AuthConfig | undefined) {
        this.storage = window.sessionStorage;
    }

    public get token$(): Observable<AuthToken> {
        return this._token$.distinctUntilChanged().share();
    }

    public get token(): AuthToken {
        return this._token;
    }

    public set token(token: AuthToken) {
        if (token !== this._token) {
            log('Access token updated.');
            if (token) {
                if (token.expired) {
                    log('Access Token is expired.');
                    token = null;
                } else {
                    this._storage.setItem(AuthTokenService.TOKEN_NAME, token.encoded);
                }
            }
            if (token == null && this._storage.getItem(AuthTokenService.TOKEN_NAME)) {
                this._storage.removeItem(AuthTokenService.TOKEN_NAME);
            }
            this._token = token;
            this._token$.next(this._token);
        }
    }

    private get storage(): Storage {
        return this._storage;
    }

    private set storage(storage: Storage) {
        this._storage = storage;
        if (!this._token && this._storage.getItem(AuthTokenService.TOKEN_NAME)) {
            log('Access token found in storage.');
            try {
                this.token = new AuthToken(this._storage.getItem(AuthTokenService.TOKEN_NAME));
            } catch (error) {
                log('Access token is malformed. Removing token from storage.');
                this._storage.removeItem(AuthTokenService.TOKEN_NAME);
            }
        }
    }
}
