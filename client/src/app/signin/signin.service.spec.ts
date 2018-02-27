import {APP_BASE_HREF} from '@angular/common';
import {inject, TestBed} from '@angular/core/testing';
import {AppModule} from '../app.module';
import {SigninModule} from './signin.module';
import {SigninService} from './signin.service';

describe('SigninService', () => {
    beforeEach(() => {
        TestBed.configureTestingModule({
            imports: [
                AppModule,
                SigninModule,
            ],
            providers: [
                {provide: APP_BASE_HREF, useValue: '/'},
            ],
        });
    });

    it('should create', inject([SigninService], (service: SigninService) => {
        expect(service).toBeTruthy();
    }));
});
