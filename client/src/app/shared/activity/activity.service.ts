import {HttpErrorResponse, HttpEvent, HttpHandler, HttpInterceptor, HttpRequest} from '@angular/common/http';
import {Injectable} from '@angular/core';
import {BehaviorSubject} from 'rxjs/BehaviorSubject';
import {Observable} from 'rxjs/Observable';

@Injectable()
export class ActivityService implements HttpInterceptor {

    private static _count = 0;
    private static _count$ = new BehaviorSubject<number>(ActivityService._count);
    private static _error = false;
    private static _error$ = new BehaviorSubject<boolean>(ActivityService._error);

    public intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        ActivityService.active = true;
        return next.handle(request)
                   .finally(() => ActivityService.active = false)
                   .catch(error => {
                       if (error instanceof HttpErrorResponse) {
                           const response = <HttpErrorResponse>error;
                           if (Math.floor(response.status / 100) === 4) {
                               ActivityService.error = true;
                           }
                       }
                       return Observable.throw(error);
                   });
    }

    public static get count$(): Observable<number> {
        return ActivityService._count$.distinctUntilChanged().share();
    }

    public static get error$(): Observable<boolean> {
        return ActivityService._error$.distinctUntilChanged().share();
    }

    public static set active(active: boolean) {
        if (active) {
            ActivityService._count += 1;
        } else {
            ActivityService._count -= 1;
        }
        if (ActivityService._count < 0) {
            throw new Error('ActivityService.count is less than 0.');
        }
        ActivityService._count$.next(ActivityService._count);
    }

    public static set error(error: boolean) {
        this._error = error;
        ActivityService._error$.next(error);
    }
}
