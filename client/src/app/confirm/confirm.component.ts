import {Component, OnDestroy, OnInit} from '@angular/core';
import {AbstractControl, FormBuilder, FormControl, FormGroup, ValidationErrors, Validators} from '@angular/forms';
import {ActivatedRoute} from '@angular/router';
import {config as passwordTestConfig, test as testPassword} from 'owasp-password-strength-test';
import {Subscription} from 'rxjs/Subscription';
import {ConfirmService} from './confirm.service';

@Component({
    selector: 'app-signup-confirm',
    templateUrl: './confirm.component.html',
    styleUrls: ['./confirm.component.scss'],
})
export class ConfirmComponent implements OnInit, OnDestroy {

    private _subscriptions: Subscription[];

    public completed = false;
    public form: FormGroup;
    public submitting = false;
    public token: string;

    constructor(private _route: ActivatedRoute, private _service: ConfirmService, fb: FormBuilder) {
        this.form = fb.group({}, {updateOn: 'change'});
        const passwordControl = new FormControl('', [Validators.required, ConfirmComponent._validatePassword]);
        const passwordConfirmControl = new FormControl({value: '', disabled: true}, [
            Validators.required,
            ConfirmComponent._validateMatches,
        ]);
        this.form.addControl('password', passwordControl);
        this.form.addControl('passwordConfirm', passwordConfirmControl);
        passwordControl.statusChanges.subscribe(status => {
            if (passwordControl.valid) {
                passwordConfirmControl.enable();
            } else {
                passwordConfirmControl.disable();
            }
        });
    }

    public ngOnInit(): void {
        this._subscriptions = [
            this._route.paramMap.subscribe(params => {
                const token = params.get('token');
                this._service.confirm(token).subscribe(confirmed => {
                    if (confirmed) {
                        this.token = token;
                    }
                });
            }),
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
        const data = this.form.value;
        data.token = this.token;
        console.log(data);
    }

    private static _validateMatches(control: AbstractControl): ValidationErrors|null {
        const passwordConfirmValue: string = control.value;
        if (passwordConfirmValue.length) {
            const passwordValue = control.parent.get('password').value;
            if (passwordConfirmValue !== passwordValue) {
                return {'match': true};
            }
        }

        return null;
    }

    public static _validatePassword(control: AbstractControl): ValidationErrors|null {
        const password: string = control.value;
        if (password.length) {
            const maxLength = 4096;
            const minLength = 8;
            let errors = Validators.minLength(minLength)(control);
            if (errors) {
                return errors;
            }
            errors = Validators.maxLength(maxLength)(control);
            if (errors) {
                return errors;
            }
            passwordTestConfig({
                allowPassphrases: true,
                maxLength: maxLength,
                minLength: minLength,
                minOptionalTestsToPass: 3,
                minPhraseLength: 24,
            });
            const result = testPassword(password);
            if (!result.strong) {
                return {'password': result.errors};
            }
        }
        return null;
    }
}
