import {trigger} from '@angular/animations';
import {Component, OnDestroy, OnInit} from '@angular/core';
import {Subscription} from 'rxjs/Subscription';
import {routeTransition} from '../shared/animations';
import {AuthService, AuthToken} from '../shared/auth';

@Component({
    selector: 'app-signup',
    templateUrl: './signup.component.html',
    styleUrls: ['./signup.component.scss'],
    animations: [
        trigger('routeTransition', [routeTransition]),
    ],
})
export class SignupComponent implements OnInit, OnDestroy {

    private _subscriptions: Subscription[];

    public token: AuthToken;

    constructor(private _auth: AuthService) {
    }

    public ngOnInit(): void {
        this._subscriptions = [
            this._auth.token$.subscribe(token => this.token = token),
        ];
    }

    public ngOnDestroy(): void {
        this._subscriptions.forEach(subscription => subscription.unsubscribe());
    }

    public logout(event?: Event): void {
        this._auth.logout(false);
    }
}
