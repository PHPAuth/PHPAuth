CAPTCHA Implementation
---------------

If `isBlocked()` returns `verify`, then a CAPTCHA code should be displayed.
The method `checkCaptcha($captcha)` is called to verify a CAPTCHA code. By default, this method returns `true` but should be overridden to verify a CAPTCHA.

For example, if you are using Google's ReCaptcha NoCaptcha, use the following code:

```php
    private function checkCaptcha($captcha)
    {
 try {

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = ['secret'   => 'your_secret_here',
            'response' => $captcha,
            'remoteip' => $this->getIp()];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return json_decode($result)->success;
    }
    catch (\Exception $e) {
        return false;
    }
}
```

If a CAPTCHA is not to be used, please ensure to set `attempt_before_block` to the same value as `attempts_before_verify`.

Also, `Auth::checkReCaptcha()` method can be called.

