@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400']) }}>
    {{ $value ?? $slot }}
</label>
