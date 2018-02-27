import {Component} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {SigninService} from '../../signin.service';

@Component({
    selector: 'app-signin-help-username',
    templateUrl: './username.component.html',
    styleUrls: ['./username.component.scss'],
})
export class UsernameComponent {

    public completed = false;
    public form: FormGroup;
    public submitting = false;

    constructor(private _service: SigninService, fb: FormBuilder) {
        this.form = fb.group({email: ['', [Validators.required, Validators.email]]});
    }

    public submit(event?: Event): void {
        if (!this.form.valid) {
            return;
        }
        this.submitting = true;
        this._service.username(this.form.value.email)
            .finally(() => this.submitting = false)
            .subscribe(result => this.completed = true);
    }
}
