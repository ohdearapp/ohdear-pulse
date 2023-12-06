@php
  $colorClasses = match($color ?? 'gray') {
    'primary' => 'bg-primary text-white',
    'blue' => 'bg-sky-100 text-sky-700',
    'green' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-700/50 dark:text-emerald-200',
    'red' => 'bg-red-100 text-red-700',
    'yellow' => 'bg-yellow-100 text-yellow-700',
    'indigo' => 'bg-indigo-100 text-indigo-700',
    'purple' => 'bg-purple-100 text-purple-700',
    'orange' => 'bg-orange-100 text-orange-700 dark:bg-orange-700/50 dark:text-orange-200',
    'amber' => 'bg-amber-100 text-amber-700',
    'gray' => 'bg-gray-150 text-gray-700',
    'pink' => 'bg-pink-100 text-pink-700',
    default => 'bg-gray-200 text-gray-800'
  };

  $sizeClasses = match($size ?? 'default') {
    'sm' => 'text-xxs px-1.5 py-[1px]',
    'lg' => 'text-xs px-2.5 py-1',
    default => 'px-2 py-0.5 text-xs'
  };
@endphp

@php
    $roundedClass = '';

    if(! str_contains($attributes->get('class'), 'rounded')){
        $roundedClass = 'rounded-full';
    }
@endphp

<div {{$attributes->merge(['class' => 'inline-block font-medium '  . $roundedClass .' '.$colorClasses. ' ' .$sizeClasses])}}>
    {{$slot}}
</div>
