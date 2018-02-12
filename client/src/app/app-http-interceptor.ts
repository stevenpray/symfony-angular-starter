import {HttpErrorResponse, HttpInterceptor} from '@angular/common/http';
import {HttpHandler} from '@angular/common/http/src/backend';
import {HttpRequest} from '@angular/common/http/src/request';
import {HttpEvent} from '@angular/common/http/src/response';
import {Injectable} from '@angular/core';
import {ActivityService} from './shared/activity';
import {NotificationService} from './shared/notification';
import {Observable} from 'rxjs/Observable';

@Injectable()
export class AppHttpInterceptorService implements HttpInterceptor {

    constructor(private _notification: NotificationService) {
    }

    public intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        return next.handle(request)
            .catch(error => {
                if (error instanceof HttpErrorResponse) {
                    const response = <HttpErrorResponse>error;
                    const status = Math.floor(response.status / 100);
                    // tslint:disable no-switch-case-fall-through
                    // noinspection FallThroughInSwitchStatementJS
                    switch (status) {
                        case 5:
                            this._notification.notify('Something went very very wrong.', 'error');
                        case 4:
                            ActivityService.error = true;
                    }
                    // tslint:enable no-switch-case-fall-through
                }
                return Observable.throw(error);
            });
    }
}
