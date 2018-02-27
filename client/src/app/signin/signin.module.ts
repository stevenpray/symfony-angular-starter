import {CommonModule} from '@angular/common';
import {HttpClientModule} from '@angular/common/http';
import {NgModule} from '@angular/core';
import {ReactiveFormsModule} from '@angular/forms';
import {MatButtonModule, MatCardModule, MatFormFieldModule, MatInputModule, MatSnackBarModule} from '@angular/material';
import {RouterModule} from '@angular/router';
import {AuthModule} from '../shared/auth';
import {PasswordComponent} from './help/password/password.component';
import {UsernameComponent} from './help/username/username.component';
import {IndexComponent} from './index/index.component';
import {signinRoutes} from './signin-routes';
import {SigninComponent} from './signin.component';
import {SigninService} from './signin.service';

@NgModule({
    declarations: [
        SigninComponent,
        UsernameComponent,
        PasswordComponent,
        IndexComponent,
    ],
    imports: [
        AuthModule,
        CommonModule,
        HttpClientModule,
        MatButtonModule,
        MatCardModule,
        MatFormFieldModule,
        MatFormFieldModule,
        MatInputModule,
        MatSnackBarModule,
        ReactiveFormsModule,
        RouterModule.forChild(signinRoutes),
    ],
    providers: [
        SigninService,
    ],
})
export class SigninModule {
}
