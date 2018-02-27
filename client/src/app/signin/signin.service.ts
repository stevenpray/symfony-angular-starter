import {Injectable} from '@angular/core';
import {Observable} from 'rxjs/Observable';
import {AuthService} from '../shared/auth';

@Injectable()
export class SigninService {

    constructor(private _auth: AuthService) {
    }

    public signin(username: string, password: string): Observable<boolean> {
        return this._auth.login(username, password);
    }

    public username(email: string): Observable<boolean> {
        return Observable.of(false);
    }

    public password(username: string): Observable<boolean> {
        return Observable.of(false);
    }
}
