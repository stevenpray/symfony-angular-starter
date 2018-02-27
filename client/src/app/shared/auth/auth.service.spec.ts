import {APP_BASE_HREF} from '@angular/common';
import {inject, TestBed} from '@angular/core/testing';
import {AppModule} from '../../app.module';
import {AuthRole} from './auth-token';
import {AuthService} from './auth.service';

describe('AuthService', () => {
    beforeEach(() => {
        TestBed.configureTestingModule({
            imports: [
                AppModule,
            ],
            providers: [
                {provide: APP_BASE_HREF, useValue: '/'},
            ],
        });
    });

    it('should create', inject([AuthService], (service: AuthService) => {
        expect(service).toBeTruthy();
    }));

    it('should not authorize', inject([AuthService], (service: AuthService) => {
        expect(service.isAuthenticated).toBeFalsy();
        Object.keys(AuthRole).forEach(key => {
            expect(service.isAuthorized(AuthRole[key])).toBeFalsy();
        });
    }));
});
