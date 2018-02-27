import {APP_BASE_HREF} from '@angular/common';
import {async, TestBed} from '@angular/core/testing';
import {AppModule} from '../app.module';
import {DefaultComponent} from './default.component';

describe('DefaultComponent', () => {
    beforeEach(async(() => {
        TestBed.configureTestingModule({
            imports: [
                AppModule,
            ],
            providers: [
                {provide: APP_BASE_HREF, useValue: '/'},
            ],
        }).compileComponents();
    }));

    it('should create', async(() => {
        const fixture = TestBed.createComponent(DefaultComponent);
        const component = fixture.debugElement.componentInstance;
        expect(component).toBeTruthy();
    }));
});
