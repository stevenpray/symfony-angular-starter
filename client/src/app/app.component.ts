import {Component, OnDestroy, OnInit} from '@angular/core';
import {RouterOutlet} from '@angular/router';
import {animate, query, style, transition, trigger} from '@angular/animations';
import {environment} from '../environments/environment';
import {AuthService} from './shared/auth';
import {Subscription} from 'rxjs/Subscription';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.scss'],
    animations: [
        trigger('routerTransition', [
            transition('* <=> *', [
                query(':enter', [
                    style({'opacity': 0}),
                    animate('300ms cubic-bezier(0.77, 0, 0.175, 1)', style('*')),
                ], {optional: true}),
            ])
        ]),
        trigger('header', [
            transition(':enter', [
                style({opacity: 0}),
                animate('300ms cubic-bezier(0.77, 0, 0.175, 1)', style({opacity: 1}))
            ]),
        ])

    ],
})
export class AppComponent implements OnInit, OnDestroy {

    private _subscriptions: Subscription[];

    public authenticated = false;
    public environment = environment;
    public title = 'Symfony-Angular Starter';

    constructor(private _auth: AuthService) {
    }

    public ngOnInit(): void {
        this._subscriptions = [
            this._auth.token$.subscribe(token => this.authenticated = token && !token.expired),
        ];
    }

    public ngOnDestroy(): void {
        this._subscriptions.forEach(subscription => subscription.unsubscribe());
    }

    public logout(event?: Event): void {
        this._auth.logout();
    }

    public getRouterTransitionState(outlet: RouterOutlet): string {
        return outlet.isActivated ? outlet.activatedRoute.snapshot.routeConfig.path : null;
    }
}
