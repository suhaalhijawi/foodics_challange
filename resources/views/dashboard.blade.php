<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
        @foreach($available_products as $key => $value)

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 p-4">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h4>{{$value->id}} - {{$value->name}}</h4>
                    <p>{{$value->description}}</p>
                    @foreach($value->ingredients as $ingredient_key => $ingredient_value)
                    <h4>{{$ingredient_value->name}} - {{$ingredient_value->pivot->amount}}{{ __('messages.gram') }}
                    </h4>
                    @endforeach
                    <div class="flex items-center gap-4 add-to-cart" data-id="{{ $value->id }}" style="float:right;margin-bottom:15px">
                        <x-primary-button>{{ __('messages.add_to_cart') }}</x-primary-button>
                    </div>
                </div>

            </div>
        </div>
        <div id="cart-message" style="display: none; color: green;"></div>
        @endforeach
    </div>

    <form method="post" action="{{ route('store_order') }}" class="mt-6 space-y-6">
        @csrf
        @method('post')
        <x-text-input id="products" name="products[]" type="hidden" autocomplete="products" />

    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".add-to-cart").forEach(button => {
                button.addEventListener("click", function() {
                    let productId = this.getAttribute("data-id");
                    var products_input = document.querySelector('#products');
                    products_input.value = productId;

                    document.getElementById("cart-message").innerText = 'success added ';
                    document.getElementById("cart-message").style.display = "block";
                    setTimeout(() => {
                        document.getElementById("cart-message").style.display = "none";
                    }, 2000);

                });
            });
        });

    </script>
</x-app-layout>
