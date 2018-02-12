import {
    HttpErrorResponse, HttpEvent, HttpHandler, HttpInterceptor, HttpRequest,
} from '@angular/common/http';
import {Injectable} from '@angular/core';
import * as debug from 'debug';
import {Observable} from 'rxjs/Observable';
import {ActivityService} from './activity.service';

const log = debug('app:activty');

@Injectable()
export class ActivityInterceptor implements HttpInterceptor {

    public intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        log('HTTP request intercepted.');
        ActivityService.active = true;
        return next.handle(request)
                   .finally(() => ActivityService.active = false)
                   .catch(error => {
                       if (error instanceof HttpErrorResponse) {
                           log('HTTP response error intercepted.');
                           ActivityService.error = true;
                       }
                       return Observable.throw(error);
                   });
    }
}
