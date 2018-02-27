import {APP_BASE_HREF} from '@angular/common';
import {async, ComponentFixture, TestBed} from '@angular/core/testing';
import {AppModule} from '../app.module';
import {ConfirmComponent} from './confirm.component';
import {ConfirmModule} from './confirm.module';

describe('ConfirmComponent', () => {
    let component: ConfirmComponent;
    let fixture: ComponentFixture<ConfirmComponent>;

    beforeEach(async(() => {
        TestBed.configureTestingModule({
                   imports: [
                       AppModule,
                       ConfirmModule,
                   ],
                   providers: [
                       {provide: APP_BASE_HREF, useValue: '/'},
                   ],
               })
               .compileComponents();
    }));

    beforeEach(() => {
        fixture = TestBed.createComponent(ConfirmComponent);
        component = fixture.componentInstance;
        fixture.detectChanges();
    });

    it('should create', () => {
        expect(component).toBeTruthy();
    });
});
