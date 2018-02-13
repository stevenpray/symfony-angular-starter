import {APP_BASE_HREF} from '@angular/common';
import {async, TestBed} from '@angular/core/testing';
import {AdminModule} from '../admin.module';
import {IndexComponent} from './index.component';

describe('IndexComponent', () => {
    beforeEach(async(() => {
        TestBed.configureTestingModule({
            imports: [
                AdminModule,
            ],
            providers: [
                {provide: APP_BASE_HREF, useValue: '/'},
            ],
        }).compileComponents();
    }));

    it('should create the component', async(() => {
        const fixture = TestBed.createComponent(IndexComponent);
        const component = fixture.debugElement.componentInstance;
        expect(component).toBeTruthy();
    }));
});
