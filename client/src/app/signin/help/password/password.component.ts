import {Component} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {SigninService} from '../../signin.service';

@Component({
    selector: 'app-signin-help-password',
    templateUrl: './password.component.html',
    styleUrls: ['./password.component.scss'],
})
export class PasswordComponent {

    public completed = false;
    public form: FormGroup;
    public submitting = false;

    constructor(private _service: SigninService, fb: FormBuilder) {
        this.form = fb.group({username: ['', Validators.required]});
    }

    public submit(event?: Event): void {
        if (!this.form.valid) {
            return;
        }
        this.submitting = true;
        this._service.password(this.form.value.username)
            .finally(() => this.submitting = false)
            .subscribe(result => this.completed = true);
    }
}
