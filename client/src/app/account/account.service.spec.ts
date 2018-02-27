import {inject, TestBed} from '@angular/core/testing';
import {AccountModule} from './account.module';
import {AccountService} from './account.service';

describe('AccountService', () => {
    beforeEach(() => {
        TestBed.configureTestingModule({
            imports: [
                AccountModule,
            ],
        });
    });

    it('should create', inject([AccountService], (service: AccountService) => {
        expect(service).toBeTruthy();
    }));
});
