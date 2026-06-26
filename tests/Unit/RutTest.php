<?php

use App\Support\Rut;

it('accepts valid ruts in different formats', function (string $rut) {
    expect(Rut::isValid($rut))->toBeTrue();
})->with([
    '11.111.111-1',
    '12.345.678-5',
    '5.126.663-3',
    '76.086.428-5',
    '111111111',
    '12345678-5',
    'rut with K verifier' => '20.347.878-K',
    'lower-case k' => '20.347.878-k',
]);

it('rejects ruts with a wrong verifier digit', function (string $rut) {
    expect(Rut::isValid($rut))->toBeFalse();
})->with([
    '12.345.678-9',
    '11.111.111-2',
    '76.086.428-1',
    '20.347.878-0',
]);

it('rejects malformed input', function (string $rut) {
    expect(Rut::isValid($rut))->toBeFalse();
})->with([
    'empty' => '',
    'letters' => 'abcdefg',
    'no verifier' => '12345678',
    'zeros only' => '0-0',
]);

it('formats a rut as 12.345.678-9', function () {
    expect(Rut::format('123456785'))->toBe('12.345.678-5');
    expect(Rut::format('20347878k'))->toBe('20.347.878-K');
});

it('masks a rut for safe logging', function () {
    expect(Rut::mask('12.345.678-5'))->toBe('12.***.**8-5');
});

it('computes the verifier digit', function () {
    expect(Rut::verifierDigit('12345678'))->toBe('5');
    expect(Rut::verifierDigit('11111111'))->toBe('1');
    expect(Rut::verifierDigit('20347878'))->toBe('K');
});
