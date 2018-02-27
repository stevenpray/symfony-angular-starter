import {Component} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {ActivatedRoute} from '@angular/router';
import {Subscription} from 'rxjs/Subscription';
import {SignupService} from '../signup.service';

@Component({
    selector: 'app-signup-index',
    templateUrl: './index.component.html',
    styleUrls: ['./index.component.scss'],
})
export class IndexComponent {

    private _subscriptions: Subscription[];

    public completed = false;
    public form: FormGroup;
    public submitting = false;

    constructor(private _route: ActivatedRoute, private _service: SignupService, fb: FormBuilder) {

        this.form = fb.group({
            firstname: ['', Validators.required],
            lastname: ['', Validators.required],
            email: ['', Validators.required, this._service.validateEmail.bind(this._service)],
            username: ['', Validators.required, this._service.validateUsername.bind(this._service)],
        });
    }

    public submit(event?: Event): void {
        if (!this.form.valid) {
            return;
        }
        this.submitting = true;
        this._service.create(this.form.value)
            .finally(() => this.submitting = false)
            .subscribe(result => this.completed = true);
    }
}
