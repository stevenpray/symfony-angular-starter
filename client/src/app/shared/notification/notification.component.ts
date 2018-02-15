import {Component, Input, OnDestroy, OnInit} from '@angular/core';
import {MatSnackBar, MatSnackBarConfig, MatSnackBarRef, SimpleSnackBar} from '@angular/material';
import {Howl} from 'howler';
import {Subscription} from 'rxjs/Subscription';
import {NotificationService} from './notification.service';

@Component({
    selector: 'app-notification',
    templateUrl: './notification.component.html',
    styleUrls: ['./notification.component.scss'],
})
export class NotificationComponent implements OnInit, OnDestroy {

    private _snackBarRef: MatSnackBarRef<SimpleSnackBar>;
    private _snackBarConfig: MatSnackBarConfig = {duration: 3000, verticalPosition: 'top'};
    private _subscriptions: Subscription[];

    @Input() public duration = 3000;
    @Input() public sounds: {[key: string]: Howl} = {};

    constructor(private _service: NotificationService, private _snackBar: MatSnackBar) {
    }

    public ngOnInit(): void {
        this._subscriptions = [
            this._service.message$.subscribe(message => {
                const config: MatSnackBarConfig = {
                    extraClasses: [message.type],
                    duration: message.type === 'info' ? this.duration / 2 : this.duration,
                    verticalPosition: 'top',
                };
                this._snackBarRef = this._snackBar.open(message.text, null, config);
                this._snackBarRef.afterOpened().subscribe(() => {
                    if (this.sounds.hasOwnProperty(message.type)) {
                        this.sounds[message.type].play();
                    }
                });
            }),
        ];
    }

    public ngOnDestroy(): void {
        this._subscriptions.forEach(subscription => subscription.unsubscribe());
    }
}
