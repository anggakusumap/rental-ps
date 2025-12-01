@extends('layouts.app')

@section('title', 'Create New Order')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Create New Food Order</h1>
            <p class="text-gray-600 mt-1">Add food and beverage items to the order</p>
        </div>

        @if($session)
            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-link text-blue-600 text-2xl mr-4"></i>
                    <div>
                        <h3 class="font-semibold text-blue-900 text-lg">Linked to Rental Session #{{ $session->id }}</h3>
                        <div class="grid grid-cols-2 gap-4 mt-3 text-sm">
                            <div>
                                <p class="text-blue-600 font-medium">Console</p>
                                <p class="text-blue-900">{{ $session->console->console_number }} - {{ $session->console->consoleType->name }}</p>
                            </div>
                            <div>
                                <p class="text-blue-600 font-medium">Customer</p>
                                <p class="text-blue-900">{{ $session->customer ? $session->customer->name : ($session->customer_name ?? 'Walk-in') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Menu Items (Left Side) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-utensils text-indigo-600 mr-3"></i>
                        Menu Items
                    </h2>

                    @foreach($foodItems->groupBy('category.name') as $categoryName => $items)
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-700 mb-3 pb-2 border-b-2 border-indigo-200 flex items-center">
                                <i class="fas fa-folder text-indigo-600 mr-2"></i>
                                {{ $categoryName }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($items as $item)
                                    <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition cursor-pointer {{ $item->stock == 0 ? 'opacity-50' : '' }}"
                                         onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, {{ $item->stock }})">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-800">{{ $item->name }}</h4>
                                                @if($item->description)
                                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $item->description }}</p>
                                                @endif
                                                <div class="mt-2">
                                                    <span class="text-lg font-bold text-indigo-600">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="mt-2">
                                                    @if($item->stock > 0)
                                                        <span class="text-xs {{ $item->stock < 10 ? 'text-yellow-600' : 'text-green-600' }} font-medium">
                                                            <i class="fas fa-box mr-1"></i>Stock: {{ $item->stock }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-red-600 font-medium">
                                                            <i class="fas fa-times-circle mr-1"></i>Out of Stock
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($item->image)
                                                <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-20 h-20 object-cover rounded-lg ml-3">
                                            @else
                                                <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-pink-500 rounded-lg ml-3 flex items-center justify-center">
                                                    <i class="fas fa-utensils text-white text-2xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @if($foodItems->isEmpty())
                        <div class="text-center py-12">
                            <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-utensils text-4xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-lg">No menu items available</p>
                            <a href="{{ route('food-items.create') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 font-medium">
                                Add menu items →
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Cart (Right Side) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-shopping-cart text-green-600 mr-3"></i>
                        Order Cart
                    </h2>

                    <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
                        @csrf

                        @if($session)
                            <input type="hidden" name="rental_session_id" value="{{ $session->id }}">
                            @if($session->customer_id)
                                <input type="hidden" name="customer_id" value="{{ $session->customer_id }}">
                            @elseif($session->customer_name)
                                <input type="hidden" name="customer_name" value="{{ $session->customer_name }}">
                            @endif
                        @else
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Customer (Optional)</label>
                                <select name="customer_id" id="customerSelect" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                                    <option value="">Walk-in Customer</option>
                                    @foreach(\App\Models\Customer::where('is_active', true)->orderBy('name')->get() as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4" id="walkInNameField">
                                <label class="block text-gray-700 font-medium mb-2">Walk-in Name (Optional)</label>
                                <input type="text" name="customer_name" placeholder="Enter customer name" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                            </div>
                        @endif

                        <div id="cartItems" class="space-y-3 mb-6 max-h-96 overflow-y-auto">
                            <div id="emptyCart" class="text-center py-8 text-gray-400">
                                <i class="fas fa-shopping-cart text-4xl mb-3"></i>
                                <p>Cart is empty</p>
                                <p class="text-sm">Click on items to add</p>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="border-t-2 border-gray-200 pt-4 space-y-3">
                            <div class="flex justify-between text-xl font-bold text-gray-900 border-t-2 border-gray-300 pt-3">
                                <span>Total:</span>
                                <span class="text-indigo-600" id="total">Rp 0</span>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" disabled class="w-full mt-6 bg-gray-300 text-gray-500 px-6 py-3 rounded-lg font-semibold cursor-not-allowed">
                            <i class="fas fa-lock mr-2"></i>Add Items to Cart
                        </button>

                        <button type="submit" id="submitBtnActive" class="hidden w-full mt-6 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold shadow-lg hover:shadow-xl">
                            <i class="fas fa-check mr-2"></i>Create Order
                        </button>

                        <a href="{{ route('orders.index') }}" class="block w-full mt-3 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = {};

        // Toggle walk-in name field
        document.getElementById('customerSelect')?.addEventListener('change', function(e) {
            const walkInField = document.getElementById('walkInNameField');
            if (e.target.value === '') {
                walkInField.classList.remove('hidden');
            } else {
                walkInField.classList.add('hidden');
            }
        });

        function addToCart(itemId, itemName, itemPrice, maxStock) {
            if (maxStock === 0) {
                alert('This item is out of stock');
                return;
            }

            if (!cart[itemId]) {
                cart[itemId] = {
                    id: itemId,
                    name: itemName,
                    price: itemPrice,
                    quantity: 1,
                    maxStock: maxStock
                };
            } else {
                if (cart[itemId].quantity < maxStock) {
                    cart[itemId].quantity++;
                } else {
                    alert('Maximum stock reached for this item');
                    return;
                }
            }

            renderCart();
        }

        function updateQuantity(itemId, delta) {
            if (cart[itemId]) {
                cart[itemId].quantity += delta;

                if (cart[itemId].quantity <= 0) {
                    removeFromCart(itemId);
                    return;
                }

                if (cart[itemId].quantity > cart[itemId].maxStock) {
                    cart[itemId].quantity = cart[itemId].maxStock;
                    alert('Maximum stock reached');
                }

                renderCart();
            }
        }

        function removeFromCart(itemId) {
            if (confirm('Remove this item from cart?')) {
                delete cart[itemId];
                renderCart();
            }
        }

        function renderCart() {
            const cartItemsContainer = document.getElementById('cartItems');
            const emptyCart = document.getElementById('emptyCart');

            if (Object.keys(cart).length === 0) {
                cartItemsContainer.innerHTML = '';
                const emptyDiv = document.createElement('div');
                emptyDiv.id = 'emptyCart';
                emptyDiv.className = 'text-center py-8 text-gray-400';
                emptyDiv.innerHTML = `
                    <i class="fas fa-shopping-cart text-4xl mb-3"></i>
                    <p>Cart is empty</p>
                    <p class="text-sm">Click on items to add</p>
                `;
                cartItemsContainer.appendChild(emptyDiv);
                document.getElementById('submitBtn').classList.remove('hidden');
                document.getElementById('submitBtnActive').classList.add('hidden');
            } else {
                cartItemsContainer.innerHTML = '';

                Object.values(cart).forEach(item => {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'bg-gray-50 rounded-lg p-3 border-2 border-gray-200';
                    itemDiv.innerHTML = `
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-gray-800 flex-1">${item.name}</h4>
                            <button type="button" onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700 ml-2">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <button type="button" onclick="updateQuantity(${item.id}, -1)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 w-8 h-8 rounded-lg font-bold">
                                    -
                                </button>
                                <input type="hidden" name="items[${item.id}][food_item_id]" value="${item.id}">
                                <input type="hidden" name="items[${item.id}][quantity]" value="${item.quantity}">
                                <span class="w-16 text-center border-2 border-gray-300 rounded-lg py-1 font-semibold">${item.quantity}</span>
                                <button type="button" onclick="updateQuantity(${item.id}, 1)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 w-8 h-8 rounded-lg font-bold">
                                    +
                                </button>
                            </div>
                            <span class="font-bold text-indigo-600">Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Rp ${item.price.toLocaleString('id-ID')} each</p>
                    `;
                    cartItemsContainer.appendChild(itemDiv);
                });

                document.getElementById('submitBtn').classList.add('hidden');
                document.getElementById('submitBtnActive').classList.remove('hidden');
            }

            updateTotals();
        }

        function updateTotals() {
            let total = 0;

            Object.values(cart).forEach(item => {
                total += item.price * item.quantity;
            });

            document.getElementById('total').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        document.getElementById('orderForm').addEventListener('submit', function(e) {
            if (Object.keys(cart).length === 0) {
                e.preventDefault();
                alert('Please add items to the cart before creating an order');
            }
        });
    </script>
@endsection
