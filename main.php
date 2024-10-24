<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body>
    <section class="bg-white dark:bg-gray-900 min-h-screen flex items-center" id="home">
        <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
            <div class="text-center lg:text-left mr-auto lg:col-span-7 flex flex-col justify-center">
                <h1 class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-8xl dark:text-white mx-auto lg:mx-0">ShopEZ</h1>
                <p class="max-w-2xl mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400 mx-auto lg:mx-0">Your ultimate shopping destination! Discover a wide range of products, enjoy seamless navigation, and experience hassle-free purchasing. Shop smarter, live better with ShopEZ!</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#product-catalog" class="inline-flex items-center justify-center px-5 py-3 text-base font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                        Get started
                        <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    <a href="#contact-us" class="inline-flex items-center justify-center px-5 py-3 text-base font-medium text-center text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                        Speak to Sales
                    </a>
                </div>
            </div>
            <div class="hidden lg:flex lg:col-span-5 lg:items-center justify-center">
                <img src="obj.png" alt="mockup" class="max-w-full h-auto">
            </div>
        </div>
    </section>

    <div class="px-48 py-8 bg-gray-900 min-w-screen min-h-screen" id="product-catalog">
        <h2 class="text-3xl font-bold mt-20 mb-6 text-center text-gray-900 dark:text-white">Our Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-10">
            <?php
            $result = $conn->query("SELECT * FROM products");
            while ($row = $result->fetch_assoc()) :
            ?>
                <div class="product-card w-full mt-12 max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 flex flex-col transition-shadow duration-300">
                    <div class="p-4 h-64 flex items-center justify-center">
                        <img class="rounded-lg max-h-full w-auto object-contain" src="uploads/products/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                    </div>
                    <div class="px-5 pb-5 flex-grow">
                        <h3 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white mb-2"><?php echo $row['name']; ?></h3>
                        <p class="text-xl font-bold text-gray-900 dark:text-blue-400 mb-4">₹ <?php echo $row['price']; ?></p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-4 description">
                            <?php echo $row['description']; ?>
                        </p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button onclick="addToCart(<?php echo $row['id']; ?>)"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 w-full mb-2">
                                Add to Cart
                            </button>
                        <?php else: ?>
                            <a href="userlogin.php" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 block mb-2">
                                Add to Cart
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>


    <section class="bg-white dark:bg-gray-900" id="contact-us">
        <div class="py-8 lg:py-16 px-4 mx-auto max-w-screen-md">
            <h2 class="mb-4 mt-6 text-4xl tracking-tight font-extrabold text-center text-gray-900 dark:text-white">Contact Us</h2>
            <p class="mb-8 lg:mb-16 font-light text-center text-gray-500 dark:text-gray-400 sm:text-xl">Got a technical issue? Want to send feedback about our Products? Need details about our Products? Let us know.</p>
            <form id="contactForm" class="mt-2 space-y-8">
                <div>
                    <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Your phone no.</label>
                    <input type="tel" id="phone" name="phone" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 dark:shadow-sm-light" placeholder="Phone no" required>
                </div>
                <div>
                    <label for="subject" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Subject</label>
                    <input type="text" id="subject" name="subject" class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 dark:shadow-sm-light" placeholder="Let us know how we can help you" required>
                </div>
                <div class="sm:col-span-2">
                    <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Your message</label>
                    <textarea id="message" name="message" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg shadow-sm border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Leave a comment..."></textarea>
                </div>
                <button type="submit" class="py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-blue-700 sm:w-fit hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Send message</button>
            </form>

        </div>
    </section>


    <footer class="p-4 bg-white md:p-8 lg:p-10 dark:bg-gray-800">
        <div class="mx-auto max-w-screen-xl text-center">
            <a href="#" class="flex justify-center items-center text-5xl font-bold text-gray-900 dark:text-white">
                ShopEZ
            </a>
            <p class="my-6 text-2xl font-semibold text-gray-500 dark:text-gray-400">Shop smarter, live better with ShopEZ!</p>
            <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">Copy Right © <a href="#" class="hover:underline">ShopEZ™</a>. All Rights Reserved.</span>
        </div>
    </footer>

    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .product-card {
            transition: box-shadow 0.3s ease;
        }

        .product-card:hover {
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.3);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var readMoreLinks = document.querySelectorAll('.read-more');

            readMoreLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    var description = link.previousElementSibling;
                    description.classList.toggle('more-text');
                    if (description.classList.contains('more-text')) {
                        link.textContent = 'Read More';
                    } else {
                        link.textContent = 'Read Less';
                    }
                });
            });
        });
    </script>


    <script>
        document.getElementById('contactForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form from submitting the traditional way

            // Get form data
            var phone = document.getElementById('phone').value;
            var subject = document.getElementById('subject').value;
            var message = document.getElementById('message').value;

            // Construct the WhatsApp URL
            var whatsappMessage = "Phone: " + phone + "\n" + "Subject: " + subject + "\n" + "Message: " + message;
            var whatsappURL = "https://api.whatsapp.com/send?phone=+919400528164&text=" + encodeURIComponent(whatsappMessage);

            // Redirect to WhatsApp
            window.open(whatsappURL, '_blank');
        });
    </script>

    <script>
        function addToCart(productId) {
            fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        alert('Product added to cart successfully!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to add product to cart');
                });
        }
    </script>


    <!--Start of Tawk.to Script-->
    <!-- <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/66b4578e146b7af4a4376be3/1i4o7c201';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script> -->
    <!--End of Tawk.to Script-->
</body>

</html>