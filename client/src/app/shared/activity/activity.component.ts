import {Component, HostBinding, Input, OnDestroy, OnInit} from '@angular/core';
import {Observable} from 'rxjs/Observable';
import {Subscription} from 'rxjs/Subscription';
import {ActivityService} from './activity.service';

@Component({
    selector: 'activity',
    templateUrl: './activity.component.html',
    styleUrls: ['./activity.component.scss'],
})
export class ActivityComponent implements OnInit, OnDestroy {

    private _active = false;
    private _mode: 'determinate' | 'indeterminate' | 'buffer' | 'query' = 'indeterminate';
    private _subscriptions: Subscription[];
    private _timer = Observable.timer(1000, 0);
    private _timerSubscription: Subscription;

    @Input() public color: 'primary' | 'warn' = 'primary';

    public get mode(): string {
        return this._mode;
    }

    @HostBinding('class.active')
    public get active(): boolean {
        return this._active;
    }

    public set active(active: boolean) {
        if (active === this.active) {
            return;
        }
        if (active === false) {
            if (this._timerSubscription == null || this._timerSubscription.closed) {
                this._timerSubscription = this._timer.subscribe(() => {
                    this._active = active;
                    this._timerSubscription.unsubscribe();
                    this._timerSubscription = this._timer.subscribe(() => {
                        ActivityService.error = false;
                        this._timerSubscription.unsubscribe();
                    });
                });
            }
        } else {
            if (this._timerSubscription) {
                this._timerSubscription.unsubscribe();
            }
            this._active = active;
        }
    }

    public ngOnInit(): void {
        this._subscriptions = [
            ActivityService.count$.subscribe(count => this.active = count > 0),
            ActivityService.error$.subscribe(error => this.color = error ? 'warn' : 'primary'),
        ];
    }

    public ngOnDestroy(): void {
        this._subscriptions.forEach(subscription => subscription.unsubscribe());
    }
}
