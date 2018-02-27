import {trigger} from '@angular/animations';
import {HttpErrorResponse} from '@angular/common/http';
import {Component, OnDestroy, OnInit} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {MatSnackBarConfig} from '@angular/material';
import {ActivatedRoute, Params as RouteParams, Router} from '@angular/router';
import {Subscription} from 'rxjs/Subscription';
import {routeTransition} from '../shared/animations';
import {AuthService, AuthToken} from '../shared/auth';
import {NotificationService} from '../shared/notification';

@Component({
    selector: 'app-signin',
    templateUrl: './signin.component.html',
    styleUrls: ['./signin.component.scss'],
    animations: [
        trigger('routeTransition', [routeTransition]),
    ],
})
export class SigninComponent implements OnInit, OnDestroy {

    private static readonly DEFAULT_URL = '/account';

    private _error: string;
    private _snackBarConfig = new MatSnackBarConfig();
    private _subscriptions: Subscription[];
    private _url: string;

    public form: FormGroup;
    public completed = false;
    public submitting = false;
    public token: AuthToken;

    constructor(private _auth: AuthService,
                private _router: Router,
                private _route: ActivatedRoute,
                private _notification: NotificationService,
                fb: FormBuilder) {

        this._snackBarConfig.panelClass = 'error';
        this._snackBarConfig.duration = 3000;

        this.form = fb.group({
            username: ['', Validators.required],
            password: ['', Validators.required],
        });
    }

    public get error(): string {
        return this._error;
    }

    public set error(error: string) {
        if (error) {
            this._notification.notify(error, 'error');
        }
        this._error = error;
    }

    public ngOnInit(): void {
        this._subscriptions = [
            this._auth.token$.subscribe(token => this.token = token),
            this._route.params.subscribe((params: RouteParams) => this._url = params.url ? params.url : SigninComponent.DEFAULT_URL),
        ];
    }

    public ngOnDestroy(): void {
        this._subscriptions.forEach(subscription => subscription.unsubscribe());
    }

    public logout(event?: Event): void {
        this._auth.logout(false);
    }

    public submit(event?: Event): void {
        this.submitting = true;
        this._auth.login(this.form.value.username, this.form.value.password)
            .finally(() => this.submitting = false)
            .subscribe(
                (authenticated) => this._router.navigateByUrl(this._url),
                (response: HttpErrorResponse) => {
                    if (response.status === 401) {
                        this.error = response.error.message;
                    } else {
                        this.error = 'Oops.';
                        console.error(response);
                    }
                },
                () => this.completed = true,
            );
    }
}
