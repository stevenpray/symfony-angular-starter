import {HttpErrorResponse, HttpEvent, HttpHandler, HttpInterceptor, HttpRequest} from '@angular/common/http';
import {Injectable} from '@angular/core';
import {Observable} from 'rxjs/Observable';
import {log} from './activity.module';
import {ActivityService} from './activity.service';

@Injectable()
export class ActivityInterceptor implements HttpInterceptor {

    public intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        log('HTTP activity start.');
        ActivityService.active = true;
        return next.handle(request)
                   .finally(() => {
                       log('HTTP activity stop.');
                       ActivityService.active = false;
                   })
                   .catch(error => {
                       if (error instanceof HttpErrorResponse) {
                           log('HTTP error.');
                           ActivityService.error = true;
                       }
                       return Observable.throw(error);
                   });
    }
}
