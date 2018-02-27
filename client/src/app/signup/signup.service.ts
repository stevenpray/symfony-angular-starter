import {HttpClient} from '@angular/common/http';
import {Injectable} from '@angular/core';
import {AbstractControl, Validators} from '@angular/forms';
import {ValidationErrors} from '@angular/forms/src/directives/validators';
import {Observable} from 'rxjs/Observable';
import {environment} from '../../environments/environment';

@Injectable()
export class SignupService {

    constructor(private _http: HttpClient) {
    }

    public create(data: any): Observable<boolean> {
        return Observable.of(false);
    }

    public validateUsername(control: AbstractControl): Observable<ValidationErrors|null> {
        if (control.errors) {
            return Observable.of(null);
        }
        const username: string = control.value;
        const url = environment.urls.api + '/signup/check-username' + '/' + username.toLowerCase();
        return this._http.get(url, {observe: 'response'})
                   .catch(error => Observable.of(error))
                   .map(response => {
                       switch (response.status) {
                           case 200:
                               return false;
                           case 409:
                               return true;
                           default:
                               return null;
                       }
                   })
                   .map(exists => exists ? {'exists': true} : null);
    }

    public validateEmail(control: AbstractControl): Observable<ValidationErrors|null> {
        if (control.errors) {
            return Observable.of(null);
        }
        const email: string = control.value;
        const errors = Validators.email(control);
        if (errors) {
            return Observable.of(errors);
        }
        const url = environment.urls.api + '/signup/check-email' + '/' + email;
        return this._http.get(url, {observe: 'response'})
                   .catch(error => Observable.of(error))
                   .map(response => {
                       switch (response.status) {
                           case 400:
                               return {'email': true};
                           case 409:
                               return {'exists': true};
                           default:
                               return null;
                       }
                   });
    }
}
