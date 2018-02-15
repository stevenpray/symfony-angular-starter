import {trigger} from '@angular/animations';
import {Component} from '@angular/core';
import {routeTransition} from '../app.animations';

@Component({
    selector: 'app-admin',
    templateUrl: './admin.component.html',
    styleUrls: ['./admin.component.scss'],
    animations: [
        trigger('routeTransition', [routeTransition]),
    ],
})
export class AdminComponent {
}
