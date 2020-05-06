<?php
/**
 * 2017-07-21 by 我是个导演
 * 欢迎访问支付宝论坛：https://openclub.alipay.com/index.php
 * 
 * APP支付 RSA2签名方法
 */
require_once 'AopSdk.php';
$aop = new AopClient ();
// $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
$aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';//沙箱网关
$aop->appId = '2016092500594097';
$aop->rsaPrivateKey = 'MIIEpAIBAAKCAQEArmn3PizK4R/g4OpFqJ77b+mrxgJI2RZIqgc96rGjwmEPFkY0f9N0urrfkkTIhUnqM5IFE0I0CfSFbKTFHzX68iIsYHS6pb0mWFefPfX2ioP5nJFLL0HcyHw+WoBk/+8aSqzJ6OI5LQ4B9OsVaA9U6mE3sJyBfZMnU1l6r6JlKr1YiwdUo5sRsOPcdHCU9BB62b12gyepblEriiPI5WOcA0k3WwIU9ausWC4BnRjPz9cZrhIkAFLtmxTke3LYEkbLagYcaa+YxfarLOLYa6yY1YX1riXUP8Mf+e3W0Joi8SyG7KmxrP2jamHwE0UgpcbEBHGNZjXWEVRXSXe+z02hjwIDAQABAoIBAQCK9RXszVceIX3S9BNnkrKUqUEX0v4jJyPhgz+LWtgzp4yTnH97UAdyNiylpnNz7j3PtIiinV5EiDI9KtF6WlGC1EMy3g1OuvJv8++FOA+isB8Q5JlYH0s91+79v8m4NtFlqWB8ULBo+v4IGbvGWmC3E5mA9lAcsj7koyeiAupDL6KdtO9mNxv7422l2gKolxRHK8QHOM3Ig5c2Odf7ssFaB+yascw/mNaBothw7wfDIU5GgAw0EketPcbRnRMF5PcNb6j7LmkKcRP0FBnASiIpeO825qjxcQjZmCfGnnggigBmnYm2JYlRZgSGrcrQRvYT8BYLti5sfsHoAYSxp2UhAoGBAODphrQaGQqvt20ApoJUd1ESSrDQx7lfBVuXokGTqXl0GRyKtjUlUglv9Au1rYvumsL+jn+87sLWxJvs8SJ4v2tO/daEsVZq/sKa0lcPhj+PjLNfyUxJAR5F7QZ02Ef4GwCYO1ef9j5GSC3xEkrNV4HBEAZIWtS0MVoi87e9eeTXAoGBAMaFkWO8TKnJJ+rLDYY7GZXjBaM7/rzbcjaQ3UzPbw6Aq0g2Iy91pCPrciW83UsbHTVHN9lj9rnL7PNMWPQO02osl0xhvt0WMJ+Pi0tbhdXboltNSI61tM5+gfMWNpv+wVQK6tMxexEhUave+m9TUm2UsJoSfUzqB8kd/M1d11oJAoGAKjVJFUWMi4fcaXVkyjKlza4cHECiKrSdYcn8Jkha1rMl/0g8145wbdr3trbaodcebhetkGVZfXEmpoh53FlCuxWw53Axg6FCvCyn6rS8IfODmSoRseJFdnPy/nIxIJh8IMlw4YnggWFZLF4Aa89La8tagYNUoHRMirjiegnx9TMCgYEAsQI5b6de5+ivSeABUBV0K7w/s5tz+GrmvJaQa8Ntb430qy9yUgff8hMmNVXgLu6fLXkx6AkE3gdhYFJL4lkx0rKtuPJSn4kAk9UGy4twmw36BtSzBGQqMhCEx/2fdZpCQTB2zxaId+6whsILpKWzkneIu+gOhz0I8h9l4KtysBkCgYB9sQl2qWbZPu7MzNhhdpaC4ZIeYpiyUtYKwxeDLspCDdjL9nMbS3zBr/zGuiMd9iCk3RSxJ2iqhqv7H8wJoCgNeksGo48lZiw3+vDZ5Op7AEK8j7qbicPnP6jqyc9rX5a0KkLswhjGhikYSj//mYbn9ryPjulBg/NbFWbUzLI4sg==';
$aop->alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwMfGrnvgWesHorcs+CUbBT6ctfjqFfTZ9bOEVyfGsEtrv/1AFD/lm6rbgxshiWng4RgrFhwc3adhMnbAdbTG3ViPMyC5RPH5slckp9ZWHQjpdYKPwVOJWlhzuN/2TVsDUvJ7NtYjhbyPUv2tZkoHxhRHzz8YkBG6+DglkR20AE/PQwHqJdPTZ/crg7TE6B4SqD6DKwnEv14r9Z8KlCS8TpZaVcHVOwEh5kz8Nug5aDRpzuruSIP+kivq+iMaEY84FlsYS0Q03Je6cVdjzHiXc2knIzEqqMeSCrS8jWbhURh1WmMjpp7O35VCWoCEEkiy6ErwN7DfBonLikvoYzzMIwIDAQAB';
$aop->apiVersion = '1.0';
$aop->postCharset='utf-8';
$aop->format='json';
$aop->signType = 'RSA2';
//生成随机订单号
$date=date("YmdHis");
$arr=range(1000,9999);
shuffle($arr);
$request = new AlipayTradeAppPayRequest();
//异步地址传值方式
$request->setNotifyUrl("https://www.alipay.com");
$request->setBizContent("{\"out_trade_no\":\"".$date.$arr[0]."\",\"total_amount\":0.01,\"product_code\":\"QUICK_MSECURITY_PAY\",\"subject\":\"app测试\"}");
$result = $aop->sdkExecute($request);
print_r(htmlspecialchars($result));
?>


