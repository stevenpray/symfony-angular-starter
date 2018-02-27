import {Injectable} from '@angular/core';
import {Observable} from 'rxjs/Observable';

@Injectable()
export class ConfirmService {

    public confirm(token: string): Observable<boolean> {
        return Observable.of(token === 'xxx');
    }
}
