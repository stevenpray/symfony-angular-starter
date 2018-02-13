import {APP_BASE_HREF} from '@angular/common';
import {async, TestBed} from '@angular/core/testing';
import {RouterModule} from '@angular/router';
import {AdminComponent} from './admin.component';
import {AdminModule} from './admin.module';
import {adminRoutes} from './admin.routes';

describe('AdminComponent', () => {
    beforeEach(async(() => {
        TestBed.configureTestingModule({
            imports: [
                AdminModule,
                RouterModule.forRoot(adminRoutes),
            ],
            providers: [
                {provide: APP_BASE_HREF, useValue: '/'},
            ],
        }).compileComponents();
    }));

    it('should create the component', async(() => {
        const fixture = TestBed.createComponent(AdminComponent);
        const component = fixture.debugElement.componentInstance;
        expect(component).toBeTruthy();
    }));
});
