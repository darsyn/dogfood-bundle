Dogfood Bundle
==============

**Eating your own dog food**, also called *dogfooding*, is a slang term used to reference a scenario in which a company uses its own product to test and promote the product.

With inspiration from [Alex Bilbie](http://linkey.blogs.lincoln.ac.uk/2013/03/22/api-driven-development-eating-your-own-dog-food/) and [Jani Tarvainen](https://www.symfony.fi/entry/dogfooding-rest-apis), this project is a proof-of-concept written as a [Symfony2](http://symfony.com) bundle.

*Not currently considered stable or appropriate for production.*

The State of APIs
-----------------

A lot of companies I have worked at have built a website, and *then* built the API. It's an after-thought.
All of those companies however are small and can't afford the time or resources to properly implement microservice architecture.

This unfortunately meant that the API that was meant to compliment the product slowly lagged behind, as focus was on adding and improving features to the website - when demand for an API rose (for example, due to the boom in mobile usage) the project couldn't cope without major refactoring that could have been avoided.

> If you have yet to build an API, I recommend reading
> [Building APIs You Won't Hate](https://leanpub.com/build-apis-you-wont-hate). Totally worth it.

This bundle aims to bridge that gap by emulating some of the core design practices of microservices.

### How?

Dirty-hack-because-it-actually-uses-master-not-sub sub-requests via the HTTP kernel.

Yes, having two master requests within the lifetime of a Symfony application does cause issues. Yes, I am still finding out what those issues are.

### What This Bundle Isn't

- Microservice architecture for your Symfony projects.
- The holy grail of API bundles.
- Suitable for larger projects.

### What This Bundle Is

- A convinient way to get into the mindset of API-centric applications.
- Microservice architecture emulation (no need for multiple servers or application instances).
- Meant to compliment other API bundles such as [FOSRestBundle](https://github.com/friendsofsymfony/FOSRestBundle).

This bundle is not intended to ever be a permant fixture or development tool. The moment you are comfortable with the concepts, you should plan your next project without it.

The Concept
-----------

Imagine you have the following, simplified controller:

```php
<?php
namespace AcmeBundle\Controller;

use AcmeBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * @Route("/product/create")
     */
    public function createAction()
    {
        $product = new Product;
        $product->setName('A Foo Bar');
        $product->setPrice('19.99');
        $product->setDescription('Lorem ipsum dolor');

        $em = $this->getDoctrine()->getManager();

        $em->persist($product);
        $em->flush();

        return $this->render('AcmeBundle:Product:create.html.twig', [
            'name' => $product->getName(),
        ]);
    }
}
```

The web controller cannot be re-used. Sure, we can refactor our code using OOP principles but that doesn't help the fact that the web controller is the main entry point for this create product functionality.

API controllers, however, deal purely in data without the contamination of HTML templates. If we put all of our logic into an API controller, we can create a "dogfood" request to the API.

```php
<?php
namespace AcmeBundle\Controller;

use AcmeBundle\Entity\Product;
use Darsyn\Bundle\DogfoodBundle\Response\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Darsyn\Bundle\DogfoodBundle\Controller\Controller as DogfoodController;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends DogfoodController
{
    /**
     * @Route("/product/create")
     */
    public function createAction()
    {
        $apiResponse = $this->dogfood('POST', '/api/product/create');
        return $this->render(
            'AcmeBundle:Product:create.html.twig',
            $apiResponse->getData()
        );
    }

    /**
     * @Route("/api/product/create")
     */
    public function apiCreateAction()
    {
        $product = new Product;
        $product->setName('A Foo Bar');
        $product->setPrice('19.99');
        $product->setDescription('Lorem ipsum dolor');

        $em = $this->getDoctrine()->getManager();

        $em->persist($product);
        $em->flush();

        return new ApiResponse(['name' => $product->getName()]);
    }
}
```

- You are now developing an API-centric app!
- Web controllers now consist of two steps: getting data from the appropriate end-point, and passing that data to the template renderer.
- All of your logic is in the API. Now you only need to unit test your API controllers and functional test your web controllers.

**N.B.** `ApiResponse` is just an extension of Symfony's `JsonResponse`. It outputs JSON if the API was requested, and provides the `getData()` method if the API was requested internally.

Creating Requests
-----------------

There are two alternative methods for creating internal dogfood requests:

```php
<?php
use Symfony\Component\HttpFoundation\Request;

$apiResponse = $this->dogfoodRoute('name_of_route', $parameters = [], $data);

$request = Request::create();
$apiResponse = $this->dogfoodRequest($request);

```

Data Strategies
---------------
