import {HttpErrorResponse, HttpEvent, HttpHandler, HttpInterceptor, HttpRequest} from '@angular/common/http';
import {Injectable} from '@angular/core';
import {BehaviorSubject} from 'rxjs/BehaviorSubject';
import {Observable} from 'rxjs/Observable';

export interface NotificationMessage {
    text: string;
    type?: 'info'|'warning'|'error';
}

@Injectable()
export class NotificationService implements HttpInterceptor {

    private _message$ = new BehaviorSubject<NotificationMessage>(null);

    public get message$(): Observable<NotificationMessage> {
        return this._message$.distinctUntilChanged().filter(notification => notification != null).share();
    }

    public notify(message: string, type: 'info'|'warning'|'error' = 'info'): void {
        this._message$.next({text: message, type: type});
    }

    public intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        return next.handle(request)
                   .catch(error => {
                       if (error instanceof HttpErrorResponse) {
                           const response = <HttpErrorResponse>error;
                           if (Math.floor(response.status / 100) === 5) {
                               this.notify('Something went very very wrong.', 'error');
                           }
                       }
                       return Observable.throw(error);
                   });
    }
}
