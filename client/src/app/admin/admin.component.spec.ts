import {APP_BASE_HREF} from '@angular/common';
import {async, TestBed} from '@angular/core/testing';
import {AppModule} from '../app.module';
import {AdminComponent} from './admin.component';
import {AdminModule} from './admin.module';

describe('AdminComponent', () => {
    beforeEach(async(() => {
        TestBed.configureTestingModule({
            imports: [
                AdminModule,
                AppModule,
            ],
            providers: [
                {provide: APP_BASE_HREF, useValue: '/'},
            ],
        }).compileComponents();
    }));

    it('should create', async(() => {
        const fixture = TestBed.createComponent(AdminComponent);
        const component = fixture.debugElement.componentInstance;
        expect(component).toBeTruthy();
    }));
});
