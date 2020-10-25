---
layout: post
title: Building a Simple POS System With CodeIgniter (PHP Framework)
date: 2020-10-21 23:00:00
comments: true
categories: [development, desarrollo, php]
image: /images/posts/building-a-simple-pos-system-with-php.jpg
---

On the [latest post][latest] I made this project using just PHP and JS, but, on this post I'm gonna show you how I make the same project (A simple POS System) but this time using CodeIgniter (Yes, the PHP framework), if you want to see all the project completed or just get the code, you can find it on [this GitHub repository][repo] (Just click the link).

## Requirements
- An IDE or Text editor, if you need some recommendation, please see [Visual Studio Code](https://code.visualstudio.com/) (Available for Mac, Linux and Windows)
  - _This is just required if you want to create the project step by step or modify the files._
- [Composer][composer] Installed
  - _Yes, you also need to review the [CodeIgniter Requirements][codeigniter-requirements]_
- A database instance, including their user.
  - _You can use [MySQL][mysql], [PostgreSQL][postgresql] or any other [database supported by CodeIgniter][codeigniter-requirements]_

## Some Notes Before Start
- You can run the project using
```sh
$ php spark serve
```
- All the documentation for CodeIgniter 4 is [available here][codeigniter-docs].
- All of the code for this project is available on the [Github Repository][repo].

## Some screenshots
!["POS - Pick the products"][image-1]
!["POS - Check the bill, Print or Generate New"][image-2]

## Let's start!

### .env
On this file we need to add some details on the environment that we're working, configurations that we can update on every machine to work on that specific machine without tounching the app.
So, on this file and this case we going to set 1 flag for development purposes and the details for the database.
_If you don't know how to start with this file, you can copy the file called `env` that comes with CodeIgniter base project, name that file as `.env` on the root folder of your project_

First, we need to set our project on development mode, so we need to add this line to our `.env` file.
```sh
CI_ENVIRONMENT = development
```

Also, we need to setup our database on this file, so, you can add this next lines and update the values with your own details.

```sh
database.default.hostname = localhost
database.default.database = pos
database.default.username = pos
database.default.password = pos
database.default.DBDriver = MySQLi
```

### Model
On this part, we need to create the model (our database structure) and we need to update some files and create some others.

#### app/Database/Migrations/2020-10-21-013804_products.php
For this process we're gonna use the [CodeIgniter Migration Process][codeigniter-migrations].
_Note: On my case the file has this name, on your case, you can and maybe need to change it. The numbers on the file are a date on Timestamp and is declared as `YYYY-MM-DD-HH-II-SS`. If you need more information about this, see the section [Migration file names][codeigniter-migrations-file-names]._

On this case I'm gonna build my table with just 4 fields.
  - `product_id` The Primary Key (PK) for each product, Integer with maximum 5 digits.
  - `product_name` The name of the product, VARCHAR of maximum 100 characters.
  - `product_image` The name of the icon or image for each product, VARCHAR of maximum 200 characters.
  - `product_value` The value for each product, with maximum 10 digits and additional 2 decimal digits.

Here is my code for this migration file.

```php
<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Products extends Migration {
  protected $DBGroup  = 'default';

  public function up() {
    $this->forge->addField([
      'product_id'      =>  [
        'type'            =>  'INT',
        'constraint'      =>  5,
        'unsigned'        =>  true,
        'auto_increment'  =>  true,
      ],
      'product_name'    =>  [
        'type'            =>  'VARCHAR',
        'constraint'      =>  '100',
      ],
      'product_image'   =>  [
        'type'            =>  'VARCHAR',
        'constraint'      =>  '200',
      ],
      'product_value'   =>  [
        'type'            =>  'FLOAT',
        'constraint'      =>  '10,2',
      ],
    ]);
    $this->forge->addKey('product_id', true);
    $this->forge->createTable('products');
  }
  public function down() {
    $this->forge->dropTable('products');
  }
}
```

#### app/Models/ProductsModel.php
Let's create this file where we going to set up our Products table, on this way we can expose our table to the CodeIgniter ORM.
Feel free to update the table variable to your table, the primaryKey yo your PK, and the allowedFields for your fields on the table.

```php
<?php namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model {
  protected $DBGroup        = 'default';

  protected $table          = 'products';
  protected $primaryKey     = 'product_id';

  protected $returnType     = 'array';
  protected $allowedFields  = ['product_name', 'product_image', 'product_value'];

  protected $useTimestamps  = false;
}
```

#### app/Controllers/Migrate.php
This file is needed for launch the latest migrate file.

```php
<?php namespace App\Controllers;

use CodeIgniter\Controller;

class Migrate extends Controller {
  public function index() {
    $migrate = \Config\Services::migrations();
    
    try {
      $migrate->latest();
    } catch (\Throwable $e) {
      throw new \Exception( 'Migration failed' );
    }
  }
}
```

After creating this file, we need to run the migration to our database. For this process, we need to run the next command into our terminal.

```sh
$ php spark migrate
```

Now we have our database created, updates and ready to be used into our CodeIgniter development.

### Templating
Yes, We're gonna use some files with code that we use frequently to use as templates, on this case, just the header and the footer

#### app/Views/templates/header.php
The only thing that is important to explain here is the `<?= esc($title); ?>` part.
We're escaping any character and the `$title` variable is comming from our Controllers.

```php
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - <?= esc($title); ?></title>
    <link rel="stylesheet" href="/css/style.css">
  </head>
  <body>
```

You probably saw the link to our css on this file. You can create this file now, or wait until we can reach that point. If you want to create the file now, you can create the file: `public/css/style.css`.

#### app/Views/templates/footer.php
```php
    <footer>
      <div class="environment">
        <p>Page rendered in {elapsed_time} seconds</p>
        <p>Environment: <?= ENVIRONMENT ?></p>
      </div>
    </footer>
  </body>
</html>
```

### Product Controller

#### app/Controllers/Products.php
Here we need to create 2 paths, you can see each one as functions.
- The index page of the application and also the view to list all the products.
- The create page for products

```php
<?php namespace App\Controllers;

use App\Models\ProductsModel;
use CodeIgniter\Controller;

class Products extends Controller {
  // The index page of the application and also the view to list all the products.
  public function index( $page = 'create' ) {
    // We check if the file that contains the view for this page exists
    // If not exists we launch an Exception
    if ( !is_file( APPPATH . '/Views/products/' . $page . '.php' ) ) {
      throw new \CodeIgniter\Exceptions\PageNotFoundException( $page );
    }

    // Let's create the title for this page
    // This is where we generate the title that is added on the header.php file
    $data['title']  = 'Create New Product';

    // Let's import and echo'ing the header, the view for this page and the footer
    echo view('templates/header', $data);
    echo view('products/' . $page, $data);
    echo view('templates/footer', $data);
  }

  // The create page for products
  public function create() {
    // Import the Model that we built before to use it here.
    // CodeIgniter Model importation into Controller
    $model        = new ProductsModel();

    // Validatiosn for POST data
    $validations  = [
      'product_name'  =>  'required|min_length[5]|max_length[100]',
      'product_image' =>  'required|min_length[5]|max_length[200]',
      'product_value' =>  'required|decimal',
    ];

    // Let's validate the request method is a POST request
    // and validate that all the validations are fine.
    // If two conditions are false, redirect to /products/create/
    if ($this->request->getMethod() === 'post' && $this->validate( $validations )) {
      // Insert the data into the database table
      $model->insert([
        'product_name'  =>  $this->request->getPost('product_name'),
        'product_image' =>  $this->request->getPost('product_image'),
        'product_value' =>  $this->request->getPost('product_value'),
      ]);
      // Redirect to home after successfully insert of the data
      return redirect()->to('/');
    } else {
      $data['title']  = 'Create New Product';
      echo view('templates/header', $data);
      echo view('products/create');
      echo view('templates/footer');
    }
  }
}
```

### Product View

#### app/Views/products/products.php
This is nearly the same file of the last post. If you want to know more of the code please refeer to the [latest post][latest].
The only changes for this file are:
- Header and footer removed. The content was placed on templating files.
- Variable names inside `$product` object.
- Section changes for divs into one global section.
- Selectors on javascript that match the changes on the element DOM.
- Form action direction, from file to directory path.

```php
<section class="products">
  <div class="products-list">
    <?php
      foreach ( $products as $key => $product ) { ?>
        <div class="product" data-index="<?php echo $product['product_id']; ?>" data-name="<?php echo $product['product_name']; ?>" data-value="<?php echo $product['product_value']; ?>">
          <img src="./images/<?php echo $product['product_image']; ?>" alt="<?php echo $product['product_name']; ?>">
          <p class="product-name"><?php echo $product['product_name']; ?></p>
          <p class="product-value"><?php echo $product['product_value']; ?></p>
        </div>
      <?php
      }
    ?>
  </div>
  <div class="bill">
    <div class="bill-products">
      <h2>Productos</h2>
    </div>
    <div class="bill-client">
      <form method="POST" action="/bill">
        <div class="hidden">
          <label for="products">Products</label>
          <input type="text" name="products" id="products" placeholder="Products" value="">
        </div>
        <div>
          <label for="name">Name</label>
          <input type="text" name="name" id="name" placeholder="Client Name">
        </div>
        <div>
          <label for="id">ID</label>
          <input type="text" name="id" id="id" placeholder="Client ID">
        </div>
        <div>
          <input type="submit" value="Print">
        </div>
      </form>
    </div>
  </div>
</section>

<script>
  (function() {
    let products = document.querySelectorAll('section.products > .products-list > .product');
    let billProducts = document.querySelector('section.products > div.bill > .bill-products');
    let productsInput = document.querySelector('section.products > div.bill #products');

    productsInput.value = '';
    
    products.forEach( product => {
      product.addEventListener( 'click', function( e ) {
        let index = e.srcElement.dataset.index;
        let name = e.srcElement.dataset.name;
        let value = e.srcElement.dataset.value;

        let p = document.createElement('p');
        p.innerHTML = name + ' - $' + value;
        billProducts.appendChild( p );

        if ( productsInput.value == '' ) {
          productsInput.value += index;
        } else {
          productsInput.value += ',' + index;
        }
      });
    });
  })();
</script>
```

#### app/Views/products/create.php
Page with just a form that is the starting point of creation for each file.
The name and id attributes need to match the name of the elements added on validations on the Products Controller.

```php
<section class="create-product">
  <h2>Create New Product</h2>

  <!-- This is the place to show any validation error -->
  <?= \Config\Services::validation()->listErrors(); ?>

  <form action="/products/create" method="post">
    <div class="form-control">
      <!-- This is included to control CSRF attacks. -->
      <?= csrf_field() ?>
    </div>
    <div class="form-control">
      <label for="product_name">Product Name</label>
      <input required type="text" name="product_name" id="product_name" placeholder="Product Name">
    </div>
    <div class="form-control">
      <label for="product_image">Product Image</label>
      <input required type="text" name="product_image" id="product_image" placeholder="Product Image Name">
    </div>
    <div class="form-control">
      <label for="product_value">Product Name</label>
      <input required type="text" name="product_value" id="product_value" placeholder="Product Value">
    </div>
    <div class="form-control">
      <input type="submit" name="submit" value="Create Product" />
    </div>
  </form>
</section>
```

### Bill Controller

#### app/Controllers/Bills.php
Now we need to create the Controller for our Bills, on this case we don't need to insert any data to our database, but we need to retrieve some data.

```php
<?php namespace App\Controllers;

use App\Models\ProductsModel;
use CodeIgniter\Controller;

class Bill extends Controller {
  public function index( $page = 'bill' ) {
    // We check if the file that contains the view for this page exists
    // If not exists we launch an Exception
    if ( !is_file( APPPATH . '/Views/bills/' . $page . '.php' ) ) {
      throw new \CodeIgniter\Exceptions\PageNotFoundException( $page );
    }

    // We check if this is a POST request, otherway we send the user to the index (Products page).
    if ($this->request->getMethod() === 'post' ) {
      // Import the Model that we built before to use it here.
      // CodeIgniter Model importation into Controller
      $model  = new ProductsModel();

      // Let's format the data that we want to send to our View
      $data   = [
        // The title of the page
        'title'     =>  'Bill',
        // The details that are comming into the POST request from Products page
        'bill'      =>  [
          'client_name'   =>  $this->request->getPost('name'),
          'client_id'     =>  $this->request->getPost('id'),
          // We create an Array from the string that we're getting from the user
          'products_ids'  =>  explode( ',', $this->request->getPost('products') ),
        ],
        // Search the products on the database that the user request on their bills
        // Also, just one instance of each element
        'products'  =>  $model->find( array_unique( explode( ',', $this->request->getPost('products') ) ) ),
      ];

      // Let's import and echo'ing the header, the view for this page and the footer
      echo view('templates/header', $data);
      echo view('bills/' . $page, $data);
      echo view('templates/footer', $data);
    } else {
      return redirect()->to('/');
    }
  }
}
```

#### app/Views/bills/bill.php
This is nearly the same file of the last post. If you want to know more of the code please refeer to the [latest post][latest].
The only changes for this file are:
- Variable names inside `$product` object.
- The map process for each product into the bill

```php
<?php
  $bill['products'] = [];
  $bill['total']    = 0;
  
  // For each product into the bill that is selected by the user...
    // Search the item that contains the index into the products that comes from the database.
    // Add the product from database into the bill products for easy listing.
    // Add the price of the product to the total of the bill.
  foreach ( $bill['products_ids'] as $key => $index ) {
    $product            = array_search( $index, array_column( $products, 'product_id' ) );
    $bill['products'][] = $products[ $product ];
    $bill['total']      = $bill['total'] + $products[ $product ]['product_value'];
  }
?>
<section id="bill-print" class="bill-print">
  <div class="bill-print-header">
    <h1>Fancy Business</h1>
    <p>
      <span>Cra. 49, No. 7 Sur - 50</span>
      <span>Medellín, Antioquia</span>
    </p>
    <p>Telefono: +57 (4) 2619500</p>
    <p><?php echo date('d/m/Y H:i'); ?></p>
  </div>
  <div class="bill-print-user">
    <p class="bill-print-user-name">
      <span>Client:</span>
      <span><?php echo $bill['client_name']; ?></span>
    </p>
    <p class="bill-print-user-id">
      <span>ID:</span>
      <span><?php echo $bill['client_id']; ?></span>
    </p>
  </div>
  <div class="bill-print-products">
    <p>
      <span>Product</span>
      <span>Value</span>
    </p>
    
    <?php foreach ( $bill['products'] as $key => $product ) { ?>
      <p>
        <span><?php echo $product['product_name']; ?></span>
        <span><?php echo $product['product_value']; ?></span>
      </p>
    <?php } ?>
  </div>
  <div class="bill-print-total">
    <p>
      <span>Total:</span>
      <span><?php echo $bill['total']; ?></span>
    </p>
  </div>
</section>
<section class="bill-actions">
  <button id="print">Print</button>
  <button id="new">New</button>
</section>
<script>
  (function() {
    let printButton = document.querySelector('#print');
    let newButton = document.querySelector('#new');
    
    printButton.addEventListener( 'click', function( e ) {
      window.print();
    });
    newButton.addEventListener( 'click', function( e ) {
      window.location = '/';
    });
  })();
</script>
```

### Home Controller (A little update)

#### app/Controller/Home.php

```php
<?php namespace App\Controllers;

use App\Models\ProductsModel;
use CodeIgniter\Controller;

class Home extends BaseController {
  public function index() {
    // Import the Model that we built before to use it here.
    $model = new ProductsModel();

    // Let's format the data that we want to send to our View
    $data = [
      // Title of the page
      'title'     =>  'Products',
      // Retrieve all the products from database
      'products'  =>  $model->findAll(),
    ];

    // If products is empty or no products are created, let's throw an error asking for a new product.
    // To create a new products is needed to go to /products/create/
    // You can also can change the throw error line and replace it for the next one
      // return redirect()->to('/products/create/');
    if( empty( $data['products'] ) ) {
      throw new \CodeIgniter\Exceptions\PageNotFoundException('Cannot find products. Please create some items before.');
    }

    // Let's import and echo'ing the header, the view for this page and the footer
    echo view('templates/header', $data);
    echo view('products/products', $data);
    echo view('templates/footer', $data);
  }
}
```

## Styling
As I mentioned before...
This is nearly the same file of the last post. If you want to know more of the code please refeer to the [latest post][latest].
The only changes for this file are:
- Some selectors update because the DOM updates on Views
- Making the form styling general for all the site instead of just one section
- Adding the styles for Products Create Page

#### public/css/style.css
I'm gonna add this file for parts...
If you want to get all the file, you can download it from the [repository][repo] or go to [this link directly to the file][styles].

##### General Styles
```css
body {
  color: #56514B;
  font-family: Helvetica, sans-serif;
  font-size: 16px;
  margin: 0;
  padding: 0;
}
body.bill {
  flex-direction: column;
}

footer {
  background-color: #56514B;
  text-align: center;
}
footer .environment {
  color: #E7E5DD;
  padding: 1.5rem 0 1.5rem;
}
footer .environment p {
  margin: 0;
  padding: 0.3rem 0;
}
```

##### Products Page Styles
```css
section.products {
  display: flex;
  flex-direction: row;
  min-height: 100vh;
}
section.products div.products-list {
  flex: 0 0 70%;
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: space-between;
}
section.products div.products-list > div {
  background-color: #E7E5DD;
  border-radius: 10px;
  box-sizing: border-box;
  padding: 1rem;
  position: relative;
  margin: 1rem;
  flex: 0 0 20%;
  text-align: center;
  cursor: pointer;
}
/* Solving click event bug */
section.products div.products-list > div:after {
  content: '';
  display: block;
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
}
section.products div.products-list > div > img {
  max-height: 5rem;
  max-width: 5rem;
  margin-bottom: 1rem;
}
section.products div.products-list > div > p {
  margin: 0;
  padding: 0;
}

section.products div.bill {
  background-color: #E7E5DD;
  box-sizing: border-box;
  flex: 0 0 30%;
  padding: 2rem;
}
section.products div.bill > .bill-products {
  border-bottom: 1px solid #BDBBAD;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
}
section.products div.bill > .bill-products p {
  margin: 0;
}
```

##### Forms Styling
```css
form .hidden {
  display: none;
}
form > div {
  margin-bottom: 0.5rem;
}
form label {
  display: none;
}
form input[type="text"] {
  display: block;
  background: transparent;
  border: 1px solid #BDBBAD;
  border-radius: 5px;
  box-sizing: border-box;
  color: #56514B;
  font-size: 1rem;
  padding: 0.5rem 1.5rem;
  width: 100%;
}
form input[type="submit"] {
  display: block;
  background: #56514B;
  border: 1px solid #56514B;
  border-radius: 5px;
  box-sizing: border-box;
  color: #E7E5DD;
  font-size: 1rem;
  padding: 0.5rem 1.5rem;
  transition: 0.3s ease-in-out;
  width: 100%;
}
input[type="submit"]:hover {
  background: #E7E5DD;
  border: 1px solid #56514B;
  color: #56514B;
}
```

##### Create Product Pages Styles
```css
section.create-product {
  background-color: #E7E5DD;
  padding: 2rem;
  min-height: 100vh;
}
section.create-product h2 {
  text-align: center;
}
section.create-product form {
  margin: 2rem auto;
  max-width: 400px;
}
```

##### Bill Page Styles
```css
section.bill-print {
  display: block;
  border: 1px solid #56514B;
  box-sizing: border-box;
  margin: 2rem auto;
  max-width: 350px;
  padding: 2rem 1.5rem;
  width: 100%;
}
section.bill-print h1 {
  margin: 0;
}
section.bill-print p {
  margin: 0;
}
section.bill-print p span {
  display: inline-block;
}
section.bill-print .bill-print-user p span:last-of-type,
section.bill-print .bill-print-products p span:last-of-type,
section.bill-print .bill-print-total p span:last-of-type {
  font-weight: bold;
  text-align: right;
  float: right;
}
section.bill-print .bill-print-products p:first-of-type {
  font-weight: bold;
}
section.bill-print .bill-print-header {
  text-align: center;
}
section.bill-print .bill-print-user {
  border-top: 1px solid #56514B;
  border-bottom: 1px solid #56514B;
  margin: 1rem 0;
  padding: 0.5rem 0;
}
section.bill-print .bill-print-total p {
  border-top: 1px solid #56514B;
  margin-top: 1rem;
  padding-top: 0.5rem;
}
section.bill-actions button {
  display: block;
  background: #56514B;
  border: 1px solid #56514B;
  border-radius: 5px;
  box-sizing: border-box;
  color: #E7E5DD;
  font-size: 1rem;
  margin: 1rem auto;
  max-width: 350px;
  padding: 0.5rem 1.5rem;
  transition: 0.3s ease-in-out;
  width: 100%;
}
section.bill-actions button:hover {
  background: #E7E5DD;
  border: 1px solid #56514B;
  color: #56514B;
}
```

##### Print Styles
```css
@media print {
  body {
    min-height: unset;
  }
  section.bill-actions {
    display: none;
  }
}
```

## 

Now all the project is completed. You can run the project using
```sh
$ php spark serve
```

---

Remember, - All of the code for this project is available on the [Github Repository][repo].


[repo]: https://github.com/JosueDanielBust/Simple-POS-System-CodeIgniter
[latest]: ../2020-08/building-a-simple-pos-system-with-php.html
[composer]: https://getcomposer.org/download/
[mysql]: https://dev.mysql.com/downloads/
[postgresql]: https://www.postgresql.org/download/
[codeigniter-docs]: https://codeigniter4.github.io/userguide/intro/index.html
[codeigniter-requirements]: https://codeigniter.com/userguide3/general/requirements.html
[codeigniter-migrations]: https://codeigniter.com/user_guide/dbmgmt/migration.html
[codeigniter-migrations-file-names]: https://codeigniter.com/user_guide/dbmgmt/migration.html#migration-file-names
[styles]: https://github.com/JosueDanielBust/Simple-POS-System-CodeIgniter/blob/master/public/css/style.css

[image-1]: https://cldup.com/VioBCJ0mPT.png "POS - Pick the products"
[image-2]: https://cldup.com/gMheIUaoij.png "POS - Check the bill, Print or Generate New"