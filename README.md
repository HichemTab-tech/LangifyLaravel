# LangifyLaravel

LangifyLaravel is a powerful language generation library for Laravel developers. It simplifies the process of creating multilingual applications by automatically generating language files for various languages based on a single source language (e.g., 'en'). Enjoy effortless localization with LangifyLaravel.


## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Example](#example)
- [Contributing](#contributing)
- [Authors](#authors)
- [License](#license)



## Features

- Generate language files for multiple languages based on a single source language.
- Command-line tool `langs:generate` to manage language file generation easily.
- Choose between two generation modes: complete the missing translations or force overwrite existing translations.
- Progress bar for tracking the language generation process.

## Installation

To install LangifyLaravel, use Composer:

```bash
composer require hichemtab-tech/langify-laravel
```

## Usage

Once installed, you can use LangifyLaravel to generate language files for your Laravel application. Here's an example of how to use it:

1- Create the source language file: Start by creating the language file for your source language (e.g., 'en') with all the string resources you want to translate. This will act as the base for generating other language files.

2- Generate language files: Use the generate command provided by LangifyLaravel to generate language files for other languages. For example, if your source language is 'en', you can run the following command:

```bash
php artisan langs:generate en
```

This will generate language files for all the languages defined in your application. By default, LangifyLaravel will complete the missing translations in the generated files based on the source language.

### Command options

#### overwrite existing translations

Force overwrite (optional): If you want to force overwrite existing translations in the generated files, you can use the --overwrite option:

```bash
php artisan langs:generate en --overwrite
```

This will force overwrite existing translations with the ones from the source language.

#### generate language files for specific languages

Customizing language generation: If you have some languages already created and want to generate translations only for specific languages, you can specify them as a comma-separated list:

```bash
php artisan langs:generate en
> Which languages do you want to generate? (comma separated) fr,es,it
```

This will generate language files only for the specified languages (in this case, 'fr', 'es', and 'it').

## Example

Assuming you have already set up the language files for 'en', and you want to generate language files for 'fr', 'es', and 'it', you can use the following command:

```bash
php artisan langs:generate en
```

```bash
> Which languages do you want to generate? (comma separated) fr,it
```

You want to generate language files for 'fr', 'es', and 'it', so you can answer :

```bash
> fr,es,it
```

## Contributing

Contributions are always welcome!

If you have any ideas, improvements, or bug fixes, please [open an issue](https://github.com/HichemTab-tech/LangifyLaravel/issues) or [submit a pull request](https://github.com/HichemTab-tech/LangifyLaravel/pulls).

## Authors

- [@HichemTab-tech](https://www.github.com/HichemTab-tech)

## License

[MIT](https://github.com/HichemTab-tech/LangifyLaravel/blob/master/LICENSE)