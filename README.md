# Send emails with Gmail OAuth2 credentials 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/iagofelicio/laravel-gmail-oauth2.svg?style=flat-square)](https://packagist.org/packages/iagofelicio/laravel-gmail-oauth2)
[![Total Downloads](https://img.shields.io/packagist/dt/iagofelicio/laravel-gmail-oauth2.svg?style=flat-square)](https://packagist.org/packages/iagofelicio/laravel-gmail-oauth2)

This Laravel package allows you to send emails securely using Gmail OAuth2 credentials, eliminating the need to store sensitive information like username and password in your application configuration.

## Support us

This package is actively maintained and free to use. If you find it helpful, consider giving back by:

* Reporting any issues or suggesting improvements through GitHub issues.
* Contributing code or documentation changes via pull requests.
* Leaving a star on the repository to show your appreciation.
Your support helps keep this project alive and thriving!
* Supporting me on Ko-fi

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/E1E3C4XVI)

## Installation

You can install the package via composer:

For PHP 8 and Laravel 9:

```bash
composer require iagofelicio/laravel-gmail-oauth2
```

For PHP 7 and Laravel 7/8:

```bash
composer require iagofelicio/laravel-gmail-oauth2:1.0.4
```

## Gmail Credentials

To obtain a Gmail OAuth2 refresh token, you can refer to the instructions provided by the PHPMailer package on Github https://github.com/PHPMailer.

For detailed instructions on generating a Gmail OAuth2 refresh token, we recommend following the excellent tutorial provided by Coding Yet https://www.codingyet.com/how-to-use-phpmailer-with-xoauth2/. 

This tutorial walks you through the process step-by-step, ensuring a smooth setup for your secure email sending.


## Usage

### (Step 1) For PHP 7 and Laravel 7/8:

Edit `config/mail.php` adding the following line:

```php
return [

    'driver' => 'gmail',
    
    // Default code ...
];
```

### (Step 1) For PHP 8 and Laravel 9:

Edit `config/mail.php` adding the following mailer:

```php
return [

    'mailers' => [
        
        // Default code ...

        'gmail' => [
            'transport' => 'gmail',
        ],
    ]    
];
```

Edit the MAIL_MAILER in `.env` file:

```bash
MAIL_MAILER=gmail
```


### (Step 2) For Laravel 7, 8 and 9:

Add your Gmail credentials in `.env` file:

```bash
GMAIL_API_CLIENT_ID="your-api-client-id"
GMAIL_API_CLIENT_SECRET="your-api-client-secret"
GMAIL_API_CLIENT_REFRESH_TOKEN="your-api-client-refresh-token"
GMAIL_API_CLIENT_MAIL="your-api-client-email"

# Suggested default settings to include
MAIL_FROM_ADDRESS="from@mail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Testing

Pending development.

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Iago Felicio](https://github.com/iagofelicio)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.