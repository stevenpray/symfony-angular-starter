import {inject, TestBed} from '@angular/core/testing';
import {ConfirmModule} from './confirm.module';
import {ConfirmService} from './confirm.service';

describe('ConfirmService', () => {
    beforeEach(() => {
        TestBed.configureTestingModule({
            imports: [
                ConfirmModule,
            ],
        });
    });

    it('should create', inject([ConfirmService], (service: ConfirmService) => {
        expect(service).toBeTruthy();
    }));
});
