import {browser, by, element} from 'protractor';

describe('client', () => {
    it('should display welcome message', () => {
        browser.get('/');
        expect(element(by.css('a[rel="home"]')).getText()).toEqual('Symfony-Angular Starter');
    });
});
