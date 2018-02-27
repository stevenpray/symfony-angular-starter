import {inject, TestBed} from '@angular/core/testing';
import {AdminModule} from './admin.module';
import {AdminService} from './admin.service';

describe('SignupService', () => {
    beforeEach(() => {
        TestBed.configureTestingModule({
            imports: [
                AdminModule,
            ],
        });
    });

    it('should create', inject([AdminService], (service: AdminService) => {
        expect(service).toBeTruthy();
    }));
});
