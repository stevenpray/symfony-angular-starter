import {CommonModule} from '@angular/common';
import {HttpClientModule} from '@angular/common/http';
import {NgModule} from '@angular/core';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {MatButtonModule, MatCardModule, MatFormFieldModule, MatIconModule, MatInputModule, MatSnackBarModule} from '@angular/material';
import {RouterModule} from '@angular/router';
import {confirmRoutes} from './confirm-routes';
import {ConfirmComponent} from './confirm.component';
import {ConfirmService} from './confirm.service';

@NgModule({
    declarations: [
        ConfirmComponent,
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
        RouterModule.forChild(confirmRoutes),
    ],
    providers: [
        ConfirmService,
    ],
})
export class ConfirmModule {
}
