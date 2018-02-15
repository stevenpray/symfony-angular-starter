import {APP_BASE_HREF} from '@angular/common';
import {async, TestBed} from '@angular/core/testing';
import {RouterModule} from '@angular/router';
import {AccountComponent} from './account.component';
import {AccountModule} from './account.module';
import {accountRoutes} from './account.routes';

describe('AccountComponent', () => {
    beforeEach(async(() => {
        TestBed.configureTestingModule({
            imports: [
                AccountModule,
                RouterModule.forRoot(accountRoutes),
            ],
            providers: [
                {provide: APP_BASE_HREF, useValue: '/'},
            ],
        }).compileComponents();
    }));

    it('should create the component', async(() => {
        const fixture = TestBed.createComponent(AccountComponent);
        const component = fixture.debugElement.componentInstance;
        expect(component).toBeTruthy();
    }));
});
