@props(['variant' => 'default', 'size' => 'md'])

@php
$baseClasses = 'inline-flex items-center font-medium rounded-full';

$sizeClasses = [
    'xs' => 'text-xs px-2 py-0.5',
    'sm' => 'text-xs px-2.5 py-0.5',
    'md' => 'text-sm px-3 py-1',
    'lg' => 'text-sm px-3.5 py-1.5',
][$size] ?? 'text-xs px-2.5 py-0.5';

$variantClasses = [
    'default' => 'bg-gray-700 text-gray-300 border border-gray-600',
    'cyan' => 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/30',
    'sky' => 'bg-sky-500/10 text-sky-400 border border-sky-500/30',
    'live' => 'bg-rose-500/20 text-rose-400 border border-rose-500/40 animate-pulse',
    'success' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
    'warning' => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
    'danger' => 'bg-red-500/10 text-red-400 border border-red-500/30',
    'purple' => 'bg-purple-500/10 text-purple-400 border border-purple-500/30',
][$variant] ?? 'bg-gray-700 text-gray-300 border border-gray-600';
@endphp

<span {{ $attributes->merge(['class' => "$baseClasses $sizeClasses $variantClasses"]) }}>
    {{ $slot }}
</span>

