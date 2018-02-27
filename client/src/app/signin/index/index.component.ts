import {HttpErrorResponse} from '@angular/common/http';
import {Component, OnDestroy, OnInit} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {ActivatedRoute, Params as RouteParams, Router} from '@angular/router';
import {Subscription} from 'rxjs/Subscription';
import {SigninService} from '../signin.service';

@Component({
    selector: 'app-signin-index',
    templateUrl: './index.component.html',
    styleUrls: ['./index.component.scss'],
})
export class IndexComponent implements OnInit, OnDestroy {

    private static readonly DEFAULT_URL = '/account';

    private _subscriptions: Subscription[];
    private _url: string;

    public completed = false;
    public form: FormGroup;
    public submitting = false;

    constructor(private _service: SigninService,
                private _router: Router,
                private _route: ActivatedRoute,
                fb: FormBuilder) {

        this.form = fb.group({
            username: ['', Validators.required],
            password: ['', Validators.required],
        });
    }

    public ngOnInit(): void {
        this._subscriptions = [
            this._route.params.subscribe((params: RouteParams) =>
                this._url = params.url ? params.url : IndexComponent.DEFAULT_URL),
        ];
    }

    public ngOnDestroy(): void {
        this._subscriptions.forEach(subscription => subscription.unsubscribe());
    }

    public submit(event?: Event): void {
        if (!this.form.valid) {
            return;
        }
        this.submitting = true;
        this._service.signin(this.form.value.username, this.form.value.password)
            .finally(() => this.submitting = false)
            .subscribe(result => {
                    this.completed = true;
                    this._router.navigateByUrl(this._url);
                },
                (response: HttpErrorResponse) => {
                    this.form.setErrors({'signin': response.error.message});
                },
            );
    }
}
