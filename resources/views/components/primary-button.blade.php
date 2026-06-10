<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary', 'style' => 'font-family:Vazirmatn,Figtree,sans-serif;']) }}>
    {{ $slot }}
</button>
