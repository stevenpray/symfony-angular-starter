import {APP_BASE_HREF} from '@angular/common';
import {async, ComponentFixture, TestBed} from '@angular/core/testing';
import {AppModule} from '../../../app.module';
import {SigninModule} from '../../signin.module';
import {PasswordComponent} from './password.component';

describe('PasswordComponent', () => {
    let component: PasswordComponent;
    let fixture: ComponentFixture<PasswordComponent>;

    beforeEach(async(() => {
        TestBed.configureTestingModule({
                   imports: [
                       AppModule,
                       SigninModule,
                   ],
                   providers: [
                       {provide: APP_BASE_HREF, useValue: '/'},
                   ],
               })
               .compileComponents();
    }));

    beforeEach(() => {
        fixture = TestBed.createComponent(PasswordComponent);
        component = fixture.componentInstance;
        fixture.detectChanges();
    });

    it('should create', () => {
        expect(component).toBeTruthy();
    });
});
