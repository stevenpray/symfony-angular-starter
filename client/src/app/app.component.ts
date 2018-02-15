import {trigger} from '@angular/animations';
import {Component, HostBinding, OnDestroy, OnInit} from '@angular/core';
import {Subscription} from 'rxjs/Subscription';
import {environment} from '../environments/environment';
import {routeTransition} from './app.animations';
import {AuthRole, AuthService} from './shared/auth';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.scss'],
    animations: [
        trigger('app', [routeTransition]),
        trigger('routeTransition', [routeTransition]),
    ],
})
export class AppComponent implements OnInit, OnDestroy {

    private _subscriptions: Subscription[];

    public isAdmin = false;
    public isAuthenticated = false;
    public environment = environment;
    public title = 'Symfony-Angular Starter';

    @HostBinding('@app') public animate = true;

    constructor(private _auth: AuthService) {
    }

    public ngOnInit(): void {
        this._subscriptions = [
            this._auth.token$.subscribe(token => {
                this.isAuthenticated = token && !token.isExpired;
                this.isAdmin = this.isAuthenticated && token.hasRole(AuthRole.ADMIN);
            }),
        ];
    }

    public ngOnDestroy(): void {
        this._subscriptions.forEach(subscription => subscription.unsubscribe());
    }

    public logout(event?: Event): void {
        this._auth.logout();
    }
}
