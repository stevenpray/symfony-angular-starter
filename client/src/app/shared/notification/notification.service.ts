import {Injectable} from '@angular/core';
import {BehaviorSubject} from 'rxjs/BehaviorSubject';
import {Observable} from 'rxjs/Observable';

export interface NotificationMessage {
    text: string;
    type?: 'info' | 'warning' | 'error';
}

@Injectable()
export class NotificationService {

    private _message$ = new BehaviorSubject<NotificationMessage>(null);

    public get message$(): Observable<NotificationMessage> {
        return this._message$.distinctUntilChanged().filter(notification => notification != null).share();
    }

    public notify(message: string, type: 'info' | 'warning' | 'error' = 'info'): void {
        this._message$.next({text: message, type: type});
    }
}
