<?php

namespace PHPSTORM_META {

    /** @noinspection PhpIllegalArrayKeyTypeInspection */
    /** @noinspection PhpUnusedLocalVariableInspection */
    $STATIC_METHOD_TYPES = [
      \Omnipay\Omnipay::create('') => [
        'Ebanx' instanceof \Omnipay\Ebanx\Gateway,
      ],
      \Omnipay\Common\GatewayFactory::create('') => [
        'Ebanx' instanceof \Omnipay\Ebanx\Gateway,
      ],
    ];
}
