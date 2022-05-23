
<!DOCTYPE html>
<html lang="en">
  <head>
  <?php
    session_start();
    include_once('config/configuration.inc.php');

    $collection = $db->product;
    $record = $collection->find();
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shoping Cart</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <!-- Style CSS -->
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jquery-3.6.0.min.js"></script>
  </head>
  <body>
      
 <main>
     <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-6 col-xs-12">
          <h4 class="badge-pill badge-light mt-3 mb-3 p-2 text-center">Products</h4>
          <div class="row">
          <?php
              foreach ($record as $prop) {
            ?>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
              <div class="shadow-sm card mb-3 product">
                <img class="product-img" src="./img/<?= $prop['productImg'] ?>" alt="prd1" onmouseover="animateImg(this)"
                onmouseout="normalImg(this)"/>
                <div class="card-body">
                  <input type="hidden" class="product-id" value="<?=$prop['_id']?>"/>
                  <h5 class="card-title text-info bold product-name"><?= $prop['productName'] ?></h5>
                  <p class="card-text text-success product-price"><?= $prop['productPrice'] ?></p>
                  <button class="btn badge badge-pill badge-secondary mt-2 float-right" type="button"
                    data-action="add-to-cart">Add to cart</button>
                </div>
              </div>
            </div>
            <?php } ?>
              </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-6 col-xs-12">
          <h4 class="badge-pill badge-light mt-3 mb-3 p-2 text-center">Cart</h4>
          <div class="cart"></div>
        </div>
      </div>
    </div>
 </main>
  
<script>
"use strict";                        
let cart = [];
let cartTotal = 0;

// load cart
var jqxhr = $.ajax( "cart.php?act=view" )
    .done(function(resp) {
      console.log('resp: ', resp);
      var cart = resp.cart;

      cart.forEach(item => {
        addToCart({
          id: item._id['$oid'],
          name: item.productName,
          price: item.productPrice,
          quantity: item.qty,
          img: 'img/' + item.productImg
        });
      });
    })
    .fail(function() {
      alert( "error" );
    });

const cartDom = document.querySelector(".cart");
const addtocartbtnDom = document.querySelectorAll('[data-action="add-to-cart"]');


function addToCart(product) {
  {

  const IsinCart = cart.filter(cartItem => cartItem.name === product.name).length > 0;
  if (IsinCart === false) {
    cartDom.insertAdjacentHTML("beforeend",`
    <div id="${product.id}" class="d-flex flex-row shadow-sm card cart-items mt-2 mb-3 animated flipInX">
      <div class="p-2">
          <img src="${product.img}" alt="${product.name}" style="max-width: 50px;"/>
      </div>
      <div class="p-2 mt-3">
          <p class="text-info cart_item_name">${product.name}</p>
      </div>
      <div class="p-2 mt-3">
          <p class="text-success cart_item_price">${product.price}</p>
      </div>
      <div class="p-2 mt-3 ml-auto">
          <button class="btn badge badge-secondary" type="button" data-action="increase-item">&plus;
      </div>
      <div class="p-2 mt-3">
        <p class="text-success cart_item_quantity">${product.quantity}</p>
      </div>
      <div class="p-2 mt-3">
        <button class="btn badge badge-info" type="button" data-action="decrease-item">&minus;
      </div>
      <div class="p-2 mt-3">
        <button class="btn badge badge-danger" type="button" data-action="remove-item">&times;
      </div>
    </div> `);

    if(document.querySelector('.cart-footer') === null){
      cartDom.insertAdjacentHTML("afterend",  `
        <div class="d-flex flex-row shadow-sm card cart-footer mt-2 mb-3 animated flipInX">
          <div class="p-2">
            <button class="btn badge-danger" type="button" data-action="clear-cart">Clear Cart
          </div>
        </div>`); 
        //clear cart
        function clearCart() {
          const cartItemsDom = cartDom.querySelectorAll(".cart-items");
          cartItemsDom.forEach(cartItemDom => {
            cartItemDom.remove();
          });
          cart = [];
          cartTotal = 0;
          if(document.querySelector('.cart-footer') !== null){
            document.querySelector('.cart-footer').remove();
          }
          addtocartbtnDom.innerText = "Add to cart";
          addtocartbtnDom.disabled = false;
          var jqxhr = $.ajax( "cart.php?act=clear")
          .done(function() {
            // alert( "success" );
            console.log('Cart removed');
          })
          .fail(function() {
            alert( "error" );
          });
        }
        document.querySelector('[data-action="clear-cart"]').addEventListener("click" , clearCart);
      }

      addtocartbtnDom.innerText = "In cart";
      addtocartbtnDom.disabled = true;
      cart.push(product);

      const cartItemsDom = cartDom.querySelectorAll(".cart-items");
      cartItemsDom.forEach(cartItemDom => {

      if (cartItemDom.querySelector(".cart_item_name").innerText === product.name) {

        // increase item in cart
        cartItemDom.querySelector('[data-action="increase-item"]').addEventListener("click", () => {
          cart.forEach(cartItem => {
            if (cartItem.name === product.name) {
              cartItemDom.querySelector(".cart_item_quantity").innerText = ++cartItem.quantity;
              cartItemDom.querySelector(".cart_item_price").innerText = parseInt(cartItem.quantity) *
              parseInt(cartItem.price);
              var jqxhr = $.ajax( "cart.php?act=update&p_id=" + product.id + "&qty=" + cartItem.quantity)
                .done(function() {
                  // alert( "success" );
                  console.log('Item increase');
                })
                .fail(function() {
                  alert( "error" );
                });
            }
          });
        });

        // decrease item in cart
        cartItemDom.querySelector('[data-action="decrease-item"]').addEventListener("click", () => {
          cart.forEach(cartItem => {
            if (cartItem.name === product.name) {
              if (cartItem.quantity > 1) {
                cartItemDom.querySelector(".cart_item_quantity").innerText = --cartItem.quantity;
                cartItemDom.querySelector(".cart_item_price").innerText = parseInt(cartItem.quantity) *
                parseInt(cartItem.price);
                var jqxhr = $.ajax( "cart.php?act=update&p_id=" + product.id + "&qty=" + cartItem.quantity)
                .done(function() {
                  // alert( "success" );
                  console.log('Item decrease');
                })
                .fail(function() {
                  alert( "error" );
                });
              }
            }
          });
        });

        //remove item from cart
        cartItemDom.querySelector('[data-action="remove-item"]').addEventListener("click", () => {
          cart.forEach(cartItem => {
            if (cartItem.name === product.name) {
                cartTotal -= parseInt(cartItemDom.querySelector(".cart_item_price").innerText);
                cartItemDom.remove();
                cart = cart.filter(cartItem => cartItem.name !== product.name);
                addtocartbtnDom.innerText = "Add to cart";
                addtocartbtnDom.disabled = false;
            }
            if(cart.length < 1){
              document.querySelector('.cart-footer').remove();
            }
          });
          var jqxhr = $.ajax( "cart.php?act=remove&p_id=" + product.id )
          .done(function() {
            // alert( "success" );
            console.log('Item removed');
          })
          .fail(function() {
            alert( "error" );
          });
        });
        

      }
    });
  }
  }
}

addtocartbtnDom.forEach(addtocartbtnDom => {
  addtocartbtnDom.addEventListener("click", () => {
    const productDom = addtocartbtnDom.parentNode.parentNode;
    const product = {
      id: productDom.querySelector(".product-id").value,
      img: productDom.querySelector(".product-img").getAttribute("src"),
      name: productDom.querySelector(".product-name").innerText,
      price: productDom.querySelector(".product-price").innerText,
      quantity: 1
   };
   addToCart(product);

   var jqxhr = $.ajax( "cart.php?act=add&p_id=" + product.id )
    .done(function() {
      // alert( "success" );
      console.log('Item added');
    })
    .fail(function() {
      alert( "error" );
    });
  });
// sessionStorage.setItem("cart",productDom);
});

function animateImg(img) {
  img.classList.add("animated","shake");
}

function normalImg(img) {
  img.classList.remove("animated","shake");
}

</script>  
  </body>
</html>