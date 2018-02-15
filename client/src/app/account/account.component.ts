import {trigger} from '@angular/animations';
import {Component} from '@angular/core';
import {routeTransition} from '../app.animations';

@Component({
    selector: 'app-account',
    templateUrl: './account.component.html',
    styleUrls: ['./account.component.scss'],
    animations: [
        trigger('routeTransition', [routeTransition]),
    ],
})
export class AccountComponent {
}
