<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary', 'style' => 'background:linear-gradient(135deg,#DC2626,#B91C1C);font-family:Vazirmatn,Figtree,sans-serif;']) }}>
    {{ $slot }}
</button>
