<?php

use Laravel\Ranger\Components\Parameter;

describe('Parameter component', function () {
    it('can be instantiated with basic properties', function () {
        $param = new Parameter('user', false, null, null);

        expect($param->name)->toBe('user');
        expect($param->optional)->toBeFalse();
        expect($param->key)->toBeNull();
        expect($param->default)->toBeNull();
    });

    it('generates placeholder for required parameters', function () {
        $param = new Parameter('user', false, null, null);

        expect($param->placeholder)->toBe('{user}');
    });

    it('generates placeholder for optional parameters', function () {
        $param = new Parameter('user', true, null, null);

        expect($param->placeholder)->toBe('{user?}');
    });

    it('stores the binding key', function () {
        $param = new Parameter('user', false, 'uuid', null);

        expect($param->key)->toBe('uuid');
    });

    it('stores the default value', function () {
        $param = new Parameter('locale', true, null, 'en');

        expect($param->default)->toBe('en');
    });

    it('has default types when no bound parameter', function () {
        $param = new Parameter('id', false, null, null);

        expect($param->types)->toBe('string | number');
    });
});
