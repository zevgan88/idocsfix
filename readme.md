
# Laravel Identity Documents

  

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![StyleCI][ico-styleci]][link-styleci]

  

For general questions and suggestions join gitter:

[![Join the chat at https://gitter.im/werk365/identitydocuments](https://badges.gitter.im/werk365/identitydocuments.svg)](https://gitter.im/werk365/identitydocuments?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

  

Package that allows you to handle documents like passports and other documents that contain a Machine Readable Zone (MRZ).

This package allows you to process images of documents to find the MRZ, parse the MRZ, parse the Visual Inspection Zone (VIZ) and also to find and return a crop of the passport picture (using face detection).

> ⚠️ Version 2.x is a complete rewrite of the package with a new MRZ detection algorithm and is not compatible with version 1.x

  
  

## Installation

  

Via Composer

  

``` bash

$ composer require werk365/identitydocuments

```

  

Publish config (optional)

``` bash

$ php artisan vendor:publish --provider="Werk365\IdentityDocuments\IdentityDocumentsServiceProvider"

```

## Configuration

### Services

The first important thing to know about the package is that you can use any OCR and or Face Detection API that you want. This package is not doing any of those itself.

#### Google Vision Service

Included with the package is a `Google` service class that will be loaded for both OCR and Face Detection by default. If you wish to use the Google service, no further configuration is required besides providing your credentials. To do this, make a service account and download the JSON key file. Then convert the JSON to a PHP array so it can be used as a normal Laravel config file. Your config file would have to be called `google_key.php`, be placed in the config folder and look like this:

```php
return [
"type" => "service_account",
"project_id" => "",
"private_key_id" => "",
"private_key" => "",
"client_email" => "",
"client_id" => "",
"auth_uri" => "",
"token_uri" => "",
"auth_provider_x509_cert_url" => "",
"client_x509_cert_url" => "",
];
```
#### Creating Custom Services
If you want to use any other API for OCR and/or Face Detection, you can make your own service, or take a look at our list of available services not included in the main package (WIP).

Making a service is relatively easy, if you want to make a service that does the OCR, all you have to do is create a class that implements `Werk365\IdentityDocuments\Interfaces\OCR`. Similarly, there is also a `Werk365\IdentityDocuments\Interfaces\FaceDetection` interface. To make creating custom services even easier you can use the following command:
```bash
$ php artisan id:service <name> <type>
```
Where `name` is the `ClassName` of the service you wish to create, and `type` is either `OCR`, `FaceDetection` or `Both`. This will create a new (empty) service for you in your `App\Services` namespace implementing the `OCR`, `FaceDetection` or both interfaces.
  

## Usage
### Basic usage
Create a new Identity Document with a maximum of 2 images (optional) in this example we'll use a POST request that includes 2 images on our example controller.
```php
use Illuminate\Http\Request;
use Werk365\IdentityDocuments\IdentityDocument;

class ExampleController {
	public function id(Request $request){
		$document = new IdentityDocument($request->front, $request->back);
	}
}
```
> ⚠️ In this example I use uploaded files, but you can use any files [supported by Intervention](http://image.intervention.io/api/make)

There are now a few things we can do with this newly created Identity Document. First of all finding and returning the MRZ:
```php
$mrz = $document->getMrz();
```

We can then also get a parsed version of the MRZ by using
```php
$parsed = $document->getParsedMrz();
```

As the MRZ only allows for A-Z and 0-9 characters, anyone with accents in their name would not get a correct first or last name from the MRZ. To (attempt to) find the correct first and last name on the VIZ part of the document, use:
```php
$viz = $document->getViz();
```
This will return an array containing both the found first and last names as well as a confidence score. The confidence score is a number between 0 and 1 and shows the similarity between the MRZ and VIZ version of the name. Please not that results can differ based on your system's `iconv()` implementation.

To get the passport picture from the document use:
```php
$face = $document->getFace()
```
This returns an `Intervention\Image\Image`

### Get all of the above
  If you wish to use all of these in a simplified way, you can also use the static `all()` method, which also expects up to two images as argument. For example:
  ```php
use Illuminate\Http\Request;
use Werk365\IdentityDocuments\IdentityDocument;

class ExampleController {
	public function id(Request $request){
		$response = IdentityDocument::all($request->front, $request->back);
		return response()->json($response);
	}
}
```
The `all()` method returns an array that looks like this:
```php
[
	'type' => 'string', // TD1, TD2, TD3, MRVA, MRVB
	'mrz' => 'string', // Full MRZ
	'parsed' => [], // Array containing parsed MRZ
	'viz' => [], // Array containing parsed VIZ
	'face' => 'string', // Base64 image string
]
```
As you can see this includes all the above mentioned methods, plus the `$document->type` variable. The detected face will be returned as a base64 image string, with an image height of 200px.

### Merging images
There are a couple of methods that will configure how the Identity Document is handled. First of all there's the `mergeBackAndFrontImages()` method. This method can be used to reduce the amount of OCR API calls have to be made. Images will be stacked on top of each other when this method is used. Please note that this method would have to be used __before__ the `getMrz()` method. Example:
```php
use Illuminate\Http\Request;
use Werk365\IdentityDocuments\IdentityDocument;

class ExampleController {
	public function id(Request $request){
		$document = new IdentityDocument($request->front, $request->back);
		$document->mergeBackAndFrontImages();
		$mrz = $document->getMrz();
	}
}
```
> ⚠️ Please note that merging images might cause high memory usage, depending on the size of your images

If you wish to use the static `all()` method and merge the images, publish the package's config file and enable it in there. Note that changing the option in the config will __only__ apply to the `all()` method. Default config value:
```php
	'mergeImages' => false, // bool
```

### Setting an OCR service
If you have made a custom OCR service or are using one different than the default Google service, you can use the `setOcrService()` method. For example let's say we've creating a new `TesseractService` using the methods described above, we can use it for OCR like this:
```php
use Illuminate\Http\Request;
use App\Services\TesseractService;
use Werk365\IdentityDocuments\IdentityDocument;

class ExampleController {
	public function id(Request $request){
		$document = new IdentityDocument($request->front, $request->back);
		$document->setOcrService(TesseractService::class);
		$mrz = $document->getMrz();
	}
}
```
If you wish to use the `all()` method, publish the package's config and set the correct service class there.

### Setting a Face Detection Service
This can be done in a similar way as the OCR service, using the `setFaceDetectionService()` method. For example:
```php
use Illuminate\Http\Request;
use App\Services\AmazonFdService;
use Werk365\IdentityDocuments\IdentityDocument;

class ExampleController {
	public function id(Request $request){
		$document = new IdentityDocument($request->front, $request->back);
		$document->setFaceDetectionService(AmazonFdService::class);
		$mrz = $document->getFace();
	}
}
```
If you wish to use the `all()` method, publish the package's config and set the correct service class there.

### Other methods
`addBackImage()` sets the back image of the `IdentityDocument`.
`addFrontImage()` sets the front image of the `IdentityDocument`.
`setMrz()` sets the `IdentityDcoument` MRZ, for if you just wish to use the parsing functionality.

## More information
If you're interested in how some things work internally, or if you would like to see an example of how to build a custom service within the package, I've written a blog post about all of that which you can find here: [hergen.nl](https://hergen.nl/processing-identity-documents-in-laravel)

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.


## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

  

## Security

If you discover any security related issues, please email <hergen.dillema@gmail.com> instead of using the issue tracker.

  

## Credits


-  [Hergen Dillema][link-author]

-  [All Contributors][link-contributors]

  

## License

. Please see the [license file](LICENSE) for more information.

  

[ico-version]: https://img.shields.io/packagist/v/werk365/identitydocuments.svg?style=flat-square

[ico-downloads]: https://img.shields.io/packagist/dt/werk365/identitydocuments.svg?style=flat-square

[ico-travis]: https://img.shields.io/travis/werk365/identitydocuments/master.svg?style=flat-square

[ico-styleci]: https://styleci.io/repos/281089912/shield

  

[link-packagist]: https://packagist.org/packages/werk365/identitydocuments

[link-downloads]: https://packagist.org/packages/werk365/identitydocuments

[link-travis]: https://travis-ci.org/werk365/identitydocuments

[link-styleci]: https://styleci.io/repos/281089912

[link-author]: https://github.com/HergenD

[link-contributors]: ../../contributors
"# idocsfix" 
