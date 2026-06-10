@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'form-input', 'style' => 'font-family:Vazirmatn,Figtree,sans-serif;']) }}>
