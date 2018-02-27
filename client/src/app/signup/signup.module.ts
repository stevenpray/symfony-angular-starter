import {CommonModule} from '@angular/common';
import {HttpClientModule} from '@angular/common/http';
import {NgModule} from '@angular/core';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {MatButtonModule, MatCardModule, MatFormFieldModule, MatIconModule, MatInputModule, MatSnackBarModule} from '@angular/material';
import {RouterModule} from '@angular/router';
import {IndexComponent} from './index/index.component';
import {signupRoutes} from './signup-routes';
import {SignupComponent} from './signup.component';
import {SignupService} from './signup.service';

@NgModule({
    declarations: [
        SignupComponent,
        IndexComponent,
    ],
    imports: [
        CommonModule,
        FormsModule,
        HttpClientModule,
        MatButtonModule,
        MatCardModule,
        MatFormFieldModule,
        MatIconModule,
        MatInputModule,
        MatSnackBarModule,
        ReactiveFormsModule,
        RouterModule.forChild(signupRoutes),
    ],
    providers: [
        SignupService,
    ],
})
export class SignupModule {
}
