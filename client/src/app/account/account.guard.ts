import {Injectable} from '@angular/core';
import {ActivatedRouteSnapshot, CanActivate, RouterStateSnapshot} from '@angular/router';
import {AuthRole, AuthService} from '../shared/auth';

@Injectable()
export class AccountGuard implements CanActivate {

    constructor(private _auth: AuthService) {
    }

    public canActivate(next: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
        return this._auth.isAuthorized(AuthRole.USER, state);
    }
}
