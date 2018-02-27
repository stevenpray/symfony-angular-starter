import {inject, TestBed} from '@angular/core/testing';
import {SignupModule} from './signup.module';
import {SignupService} from './signup.service';

describe('SignupService', () => {
    beforeEach(() => {
        TestBed.configureTestingModule({
            imports: [
                SignupModule,
            ],
        });
    });

    it('should create', inject([SignupService], (service: SignupService) => {
        expect(service).toBeTruthy();
    }));
});
