import {APP_BASE_HREF} from '@angular/common';
import {async, TestBed} from '@angular/core/testing';
import {AppModule} from '../app.module';
import {AccountComponent} from './account.component';
import {AccountModule} from './account.module';

describe('AccountComponent', () => {
    beforeEach(async(() => {
        TestBed.configureTestingModule({
            imports: [
                AccountModule,
                AppModule,
            ],
            providers: [
                {provide: APP_BASE_HREF, useValue: '/'},
            ],
        }).compileComponents();
    }));

    it('should create', async(() => {
        const fixture = TestBed.createComponent(AccountComponent);
        const component = fixture.debugElement.componentInstance;
        expect(component).toBeTruthy();
    }));
});
